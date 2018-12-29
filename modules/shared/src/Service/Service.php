<?php

namespace Chaos\Common\Service;

use Chaos\Common\Constant\ErrorCode;
use Chaos\Common\Constant\EventType;
use Chaos\Common\Contract\ConfigAware;
use Chaos\Common\Contract\ContainerAware;
use Chaos\Common\Object\Contract\ObjectTrait;
use Chaos\Common\Repository\Contract\IRepository;
use Chaos\Common\Repository\Contract\RepositoryAware;

/**
 * Class Service
 * @author ntd1712
 */
abstract class Service implements Contract\IService
{
    use ConfigAware, ContainerAware, ObjectTrait,
        RepositoryAware, /*Contract\ServiceAware, */Contract\ServiceTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $vars = $this->getVars();
        $container = $this->getContainer();

        if (!empty($repositories = func_get_args())) {
            foreach ($repositories as $repository) {
                if ($repository instanceof IRepository) {
                    $repository->setContainer($container)->setVars($vars);
                    $container->set($repository->getClass(), $repository);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool|array $paging The paging criteria; defaults to FALSE.
     * @return  array
     */
    public function readAll($criteria = [], $paging = false)
    {
        if (false !== $paging) {
            $entities = $this->repository->paginate($criteria, $paging);
        } else {
            $entities = $this->repository->readAll($criteria);
        }

        $result = ['data' => [], 'count' => count($entities), 'success' => true];

        if (0 !== $result['count']) {
            $this->trigger(EventType::ON_AFTER_READ_ALL, [CHAOS_READ_EVENT_ARGS, func_get_args(), $entities]);
            $result['data'] = $entities instanceof \Traversable ? iterator_to_array($entities) : $entities;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     */
    public function read($criteria)
    {
        if (is_scalar($criteria)) {
            if (is_numeric($criteria)) {
                $criteria = (int)$criteria;

                if (1 > $criteria) {
                    throw new Exception\ServiceException(ErrorCode::INVALID_REQUEST);
                }
            } else {
                // $criteria = $this->filter($criteria); // TODO

                if (empty($criteria)) {
                    throw new Exception\ServiceException(ErrorCode::INVALID_REQUEST);
                }
            }

            $entity = $this->repository->find($criteria);
        } else {
            if (empty($criteria)) {
                throw new \InvalidArgumentException(__METHOD__ . ' expects "$criteria" in array format');
            }

            $entity = $this->repository->read($criteria);
        }

        if (null === $entity) {
            throw new Exception\ServiceException(ErrorCode::INVALID_REQUEST);
        }

        $this->trigger(EventType::ON_AFTER_READ, [CHAOS_READ_EVENT_ARGS, $criteria, $entity]);
        $result = ['data' => $entity, 'success' => true];

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param   array $post The _POST variable.
     * @return  array
     */
    public function create(array $post = [])
    {
        return $this->update($post, null, true);
    }

    /**
     * {@inheritdoc}
     *
     * @param   array $post The _PUT variable.
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria
     * @param   bool $isNew A flag indicates we are creating or updating a record.
     * @return  array
     */
    public function update(array $post = [], $criteria = null, $isNew = false)
    {
        if (empty($post)) {
            throw new Exception\ServiceException(ErrorCode::INVALID_REQUEST);
        }

        if ($isNew) {
            if (isset($post['UpdatedAt'])) {
                $post['CreatedAt'] = $post['UpdatedAt'];
            }

            if (isset($post['UpdatedBy'])) {
                $post['CreatedBy'] = $post['UpdatedBy'];
            }

            $entity = $this->repository->entity;
        } else {
            if (null === $criteria) {
                $where = [];

                foreach ($this->repository->pk as $v) {
                    if (isset($post[$v])) {
                        $where[$v] = $post[$v];
                    }
                }

                if (!empty($where)) {
                    $criteria = ['where' => $where];
                }
            }

            $result = $this->read($criteria);
            $entity = $result['data'];
        }

        // TODO
        // $builder = new \Zend\Form\Annotation\AnnotationBuilder;
        // $form = $builder->createForm($entity);
        // //$form->bind($entity);
        // $form->setData($post);
        // var_dump($form->isValid(), $form->getMessages(), $form->getData(), $post);die;

        // exchange array
        $eventArgs = new Event\UpdateEventArgs($post, $entity, $isNew);
        $eventArgs->setPost(array_intersect_key($post, reflect($entity)->getDefaultProperties()));

        $this->trigger(EventType::ON_EXCHANGE_ARRAY, $eventArgs);
        $eventArgs->setEntity($entity->exchangeArray($eventArgs->getPost()));
        $this->trigger(EventType::ON_VALIDATE, $eventArgs);

        // TODO: validate
        // if (false !== ($errors = $entity->validate())) {
        //     throw new Exceptions\ValidateException(implode(' ', $errors));
        // }

        try {
            // start a transaction
            if ($this->enableTransaction) {
                $this->repository->beginTransaction();
            }

            $this->trigger(EventType::ON_BEFORE_SAVE, $eventArgs);

            // create or update entity
            if ($isNew) {
                $affectedRows = $this->repository->create($entity, false);
            } else {
                $affectedRows = $this->repository->update($entity, null, false);
            }

            if (1 > $affectedRows) {
                throw new Exception\ServiceException('ERROR_SAVING_ITEM');
            }

            // commit current transaction
            $this->trigger(EventType::ON_AFTER_SAVE, $eventArgs);
            $this->repository->flush()->commit();

            if ($isNew) {
                $where = [];

                foreach ($this->repository->pk as $v) {
                    $where[$v] = $entity->$v;
                }

                $criteria = ['where' => $where];
            }

            return $this->read($criteria);
        } catch (\Exception $ex) {
            $this->repository->close()->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     */
    public function delete($criteria)
    {
        $result = $this->read($criteria);
        $entity = $result['data'];

        try {
            // start a transaction
            $eventArgs = new Event\UpdateEventArgs($criteria, $entity, false);

            if ($this->enableTransaction) {
                $this->repository->beginTransaction();
            }

            $this->trigger(EventType::ON_BEFORE_DELETE, $eventArgs);

            // delete entity
            $affectedRows = $this->repository->delete($entity, false);

            if (1 > $affectedRows) {
                throw new Exception\ServiceException('ERROR_DELETING_ITEM');
            }

            // commit current transaction
            $this->trigger(EventType::ON_AFTER_DELETE, $eventArgs);
            $this->repository->flush()->commit();

            return [
                'success' => true
            ];
        } catch (\Exception $ex) {
            $this->repository->close()->rollback();
            throw $ex;
        }
    }

    // <editor-fold desc="Magic methods" defaultstate="collapsed">

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  mixed
     *
     * @deprecated We should remove this
     */
    public function __get($name)
    {
        return property_exists($this, $name) ? $this->$name : $this->repository->$name;
    }

    // </editor-fold>
}
