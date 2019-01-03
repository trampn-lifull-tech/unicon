<?php

namespace Chaos\Service;

use Chaos\Repository\Contract\IRepository;
use Chaos\Support\Constant\ErrorCode;
use Chaos\Support\Constant\EventType;
use Chaos\Support\Contract\ConfigAware;
use Chaos\Support\Contract\ContainerAware;
use Chaos\Support\Event;
use Chaos\Support\Object\Contract\ObjectTrait;

/**
 * Class ServiceHandler
 * @author ntd1712
 */
abstract class ServiceHandler implements Contract\IServiceHandler
{
    use ConfigAware, ContainerAware, ObjectTrait,
        Event\Contract\EventTrait /*, RepositoryAware, Contract\ServiceHandlerAware*/;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // <editor-fold desc="Initializes some defaults" defaultstate="collapsed">

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

        // </editor-fold>
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
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
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
            if (isset($this->repository->enableTransaction)) {
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
                throw;
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
            throw new Exception\ServiceException('ERROR_SAVING_ITEM');
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
            // start a transaction (if any)
            $eventArgs = new Event\UpdateEventArgs($criteria, $entity, false);

            if (isset($this->repository->enableTransaction)) {
                $this->repository->beginTransaction();
            }

            $this->trigger(EventType::ON_BEFORE_DELETE, $eventArgs);

            // delete entity
            $affectedRows = $this->repository->delete($entity, false);

            if (1 > $affectedRows) {
                throw;
            }

            // commit current transaction
            $this->trigger(EventType::ON_AFTER_DELETE, $eventArgs);
            $this->repository->flush()->commit();

            return [
                'success' => true
            ];
        } catch (\Exception $ex) {
            $this->repository->close()->rollback();
            throw new Exception\ServiceException('ERROR_DELETING_ITEM');
        }
    }
}
