<?php

namespace Chaos\Service;

use Chaos\Support\Config\Contract\VarsAware;
use Chaos\Support\Constant\EventType;
use Chaos\Support\Container\Contract\ContainerAware;
use Chaos\Support\Event;
use Chaos\Support\Object\Contract\ObjectTrait;
use Interop\Container\ContainerInterface;
use Zend\Filter\StaticFilter;

/**
 * Class ServiceHandler
 * @author ntd1712
 *
 * A service can use multiple repositories.
 * And one service can also use another service, but you need to manually initialize its used repositories.
 */
abstract class ServiceHandler implements Contract\ServiceHandlerInterface
{
    use ContainerAware, VarsAware,
        ObjectTrait, Event\Contract\EventTrait;

    /**
     * @var bool A value that indicates whether the transaction is enabled.
     */
    public $enableTransaction = false;

    /**
     * {@inheritdoc}
     *
     * @param   \Interop\Container\ContainerInterface $container The container object.
     * @param   object $instance [optional]
     * @return  static
     * @throws
     */
    public function __invoke(ContainerInterface $container, $instance = null)
    {
        $this->setContainer($container);
        $this->setVars($instance ?? $container->get('config'));

        $container = $this->getContainer();
        $container->set($this->getClass(), $this);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool|array $paging The paging criteria; defaults to FALSE.
     * @param   null|string|int $hydrationMode [optional] Whether the query joins a collection (true by default),
     *          or the processing mode to be used during the hydration process.
     * @return  array
     */
    public function readAll($criteria = [], $paging = false, $hydrationMode = 1)
    {
        if (false !== $paging) {
            $fetchJoinCollection = 1 === $hydrationMode;
            $entities = $this->repository->paginate($criteria, $paging, $fetchJoinCollection);
        } else {
            $entities = $this->repository->readAll($criteria, $hydrationMode);
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
     * @param   null|string|int $hydrationMode [optional] The hydration mode.
     * @return  array
     */
    public function read($criteria, $hydrationMode = null)
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
            $entity = $this->repository->read($criteria, $hydrationMode);
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
            throw new Exception\ServiceException;
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

                foreach ($this->repository->identifier as $v) {
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

        try {
            $eventArgs->setPost(array_intersect_key($post, reflect($entity)->getDefaultProperties()));
        } catch (\ReflectionException $e) {
            $eventArgs->setPost($post);
        }

        $this->trigger(EventType::ON_EXCHANGE_ARRAY, $eventArgs);
        $eventArgs->setEntity($entity->exchangeArray($eventArgs->getPost()));
        $this->trigger(EventType::ON_VALIDATE, $eventArgs);

        // TODO: validate
        // if (false !== ($errors = $entity->validate())) {
        //     throw new Exceptions\ValidateException(implode(' ', $errors));
        // }

        try {
            // before save
            !$this->enableTransaction || $this->repository->beginTransaction();
            $this->trigger(EventType::ON_BEFORE_SAVE, $eventArgs);

            // create or update
            if ($isNew) {
                $affectedRows = $this->repository->create($entity, false);
            } else {
                $affectedRows = $this->repository->update($entity, null, false);
            }

            if (1 > $affectedRows) {
                throw new Exception\ServiceException(__FUNCTION__ . '_error');
            }

            // after save
            $this->trigger(EventType::ON_AFTER_SAVE, $eventArgs);
            $this->repository->flush()->commit();

            if ($isNew) {
                $where = [];

                foreach ($this->repository->identifier as $v) {
                    $where[$v] = $entity->$v;
                }

                $criteria = ['where' => $where];
            }

            return $this->read($criteria);
        } catch (Exception\ServiceException $e) {
            $this->repository->close()->rollback();
            throw $e;
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

            !$this->enableTransaction || $this->repository->beginTransaction();
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
        } catch (Exception\ServiceException $e) {
            $this->repository->close()->rollback();
            throw $e;
        }
    }
}
