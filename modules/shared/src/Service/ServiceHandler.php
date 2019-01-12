<?php

namespace Chaos\Service;

use Chaos\Support\Constant\EventType;
use Chaos\Support\Contract\ConfigAware;
use Chaos\Support\Contract\ContainerAware;
use Chaos\Support\Event;
use Chaos\Support\Object\Contract\ObjectTrait;
use Psr\Container\ContainerInterface;
use Zend\Filter\StaticFilter;

/**
 * Class ServiceHandler
 * @author ntd1712
 */
abstract class ServiceHandler implements Contract\IServiceHandler
{
    use ConfigAware, ContainerAware, ObjectTrait,
        Event\Contract\EventTrait;

    // <editor-fold desc="InitializerInterface implementation" defaultstate="collapsed">

    /**
     * {@inheritdoc}
     *
     * @param   \Psr\Container\ContainerInterface $container The container.
     * @param   object $instance [optional]
     * @return  static
     */
    public function __invoke(ContainerInterface $container, $instance = null)
    {
        return $this
            ->setContainer($container)
            ->setVars($instance ?? $container->get(M1_VARS));
    }

    // </editor-fold>

    // <editor-fold desc="IServiceHandler implementation">

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
     * @throws  \Chaos\Service\Exception\ServiceException
     */
    public function read($criteria)
    {
        if (is_scalar($criteria)) {
            if (is_numeric($criteria)) {
                $criteria = (int)$criteria;

                if (1 > $criteria) {
                    throw new Exception\ServiceException;
                }
            } else {
                $criteria = StaticFilter::execute(
                    $criteria,
                    'HtmlEntities',
                    ['encoding' => $this->getVars()->get('app.charset')]
                );

                if (empty($criteria)) {
                    throw new Exception\ServiceException;
                }
            }

            $entity = $this->repository->find($criteria);
        } else {
            $entity = $this->repository->read($criteria);
        }

        if (empty($entity)) {
            throw new Exception\ServiceException;
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
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Chaos\Service\Exception\ValidationException
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
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Chaos\Service\Exception\ValidationException
     */
    public function update(array $post = [], $criteria = null, $isNew = false)
    {
        if (empty($post)) {
            throw new Exception\ServiceException;
        }

        if ($isNew) {
            if (isset($post['UpdatedAt'])) {
                $post['CreatedAt'] = $post['UpdatedAt'];
            }

            if (isset($post['UpdatedBy'])) {
                $post['CreatedBy'] = $post['UpdatedBy'];
            }

            $entity = new $this->repository->entity;
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
                throw new Exception\ServiceException(__FUNCTION__ . '_error');
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
        } catch (Exception\ServiceException $ex) {
            $this->repository->close()->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     * @throws  \Chaos\Service\Exception\ServiceException
     */
    public function delete($criteria)
    {
        $result = $this->read($criteria);
        $entity = $result['data'];

        try {
            // before delete
            $eventArgs = new Event\UpdateEventArgs($criteria, $entity, false);

            if (isset($this->repository->enableTransaction)) {
                $this->repository->beginTransaction();
            }

            $this->trigger(EventType::ON_BEFORE_DELETE, $eventArgs);

            // on delete
            $affectedRows = $this->repository->delete($entity, false);

            if (1 > $affectedRows) {
                throw new Exception\ServiceException(__FUNCTION__ . '_error');
            }

            // after delete
            $this->trigger(EventType::ON_AFTER_DELETE, $eventArgs);
            $this->repository->flush()->commit();

            return [
                'success' => true
            ];
        } catch (Exception\ServiceException $ex) {
            $this->repository->close()->rollback();
            throw $ex;
        }
    }

    // </editor-fold>
}
