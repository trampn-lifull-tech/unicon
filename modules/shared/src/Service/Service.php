<?php

namespace Chaos\Common\Service;

use Chaos\Common\Contract\ConfigAware;
use Chaos\Common\Contract\ContainerAware;
use Chaos\Common\Repository\Contract\RepositoryAware;

/**
 * Class Service
 * @author ntd1712
 */
abstract class Service implements Contract\IService
{
    use ConfigAware, ContainerAware, Contract\EventTrait,
        RepositoryAware, /*Contract\ServiceAware, */Contract\ServiceTrait;

    /**
     * The events being trigger.
     */
    const
        ON_AFTER_READ_ALL = 'onAfterReadAll',
        ON_AFTER_READ = 'onAfterRead',
        ON_EXCHANGE_ARRAY = 'onExchangeArray',
        ON_VALIDATE = 'onValidate',
        ON_BEFORE_SAVE = 'onBeforeSave',
        ON_AFTER_SAVE = 'onAfterSave',
        ON_BEFORE_DELETE = 'onBeforeDelete',
        ON_AFTER_DELETE = 'onAfterDelete';

    /**
     * {@inheritdoc}
     */
    public function readAll($criteria = [], $paging = false)
    {
        if (false !== $paging) {
            $entities = $this->getRepository()->paginate($criteria, $paging);
        } else {
            $entities = $this->getRepository()->readAll($criteria);
        }

        $result = ['data' => [], 'count' => count($entities), 'success' => true];

        if (0 !== $result['count']) {
            $this->trigger(self::ON_AFTER_READ_ALL, [CHAOS_READ_EVENT_ARGS, func_get_args(), $entities]);
            $result['data'] = $entities instanceof \Traversable ? iterator_to_array($entities) : $entities;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function read($criteria)
    {
        if (is_scalar($criteria)) {
            if (is_numeric($criteria)) {
                $criteria = (int)$criteria;

                if (1 > $criteria) {
                    throw new Exception\ServiceException('Your request is invalid');
                }
            } else {
                $criteria = $this->filter($criteria);

                if (empty($criteria)) {
                    throw new Exception\ServiceException('Your request is invalid');
                }
            }

            $entity = $this->getRepository()->find($criteria);
        } else {
            if (empty($criteria)) {
                throw new \InvalidArgumentException(__METHOD__ . ' expects "$criteria" in array format');
            }

            $entity = $this->getRepository()->read($criteria);
        }

        if (null === $entity) {
            throw new Exception\ServiceException('Your request is invalid');
        }

        $this->trigger(self::ON_AFTER_READ, [CHAOS_READ_EVENT_ARGS, $criteria, $entity]);
        $result = ['data' => $entity, 'success' => true];

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $post = [])
    {
        return $this->update($post, null, true);
    }

    /**
     * {@inheritdoc}
     *
     * @param   bool $isNew A flag indicates we are creating or updating a record.
     */
    public function update(array $post = [], $criteria = null, $isNew = false)
    {
        if (empty($post)) {
            throw new Exception\ServiceException('Your request is invalid');
        }

        /** @var AbstractBaseEntity $entity */
        if ($isNew) {
            if (isset($post['EditedAt'])) {
                $post['AddedAt'] = $post['EditedAt'];
            }

            if (isset($post['EditedBy'])) {
                $post['AddedBy'] = $post['EditedBy'];
            }

            $entity = $this->getRepository()->entity;
        } else {
            if (null === $criteria) {
                $where = [];

                foreach ($this->getRepository()->pk as $v) {
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

        // @todo
        // $builder = new \Zend\Form\Annotation\AnnotationBuilder;
        // $form = $builder->createForm($entity);
        // //$form->bind($entity);
        // $form->setData($post);
        // var_dump($form->isValid(), $form->getMessages(), $form->getData(), $post);die;

        // exchange array
        $eventArgs = new Event\UpdateEventArgs($post, $entity, $isNew);
        $eventArgs->setPost(array_intersect_key($post, reflect($entity)->getDefaultProperties()));

        $this->trigger(self::ON_EXCHANGE_ARRAY, $eventArgs);
        $eventArgs->setEntity($entity->exchangeArray($eventArgs->getPost()));
        $this->trigger(self::ON_VALIDATE, $eventArgs);

        // @todo: validate 'em
        // if (false !== ($errors = $entity->validate())) {
        //     throw new Exceptions\ValidateException(implode(' ', $errors));
        // }

        try {
            // start a transaction
            if ($this->enableTransaction) {
                $this->getRepository()->beginTransaction();
            }

            $this->trigger(self::ON_BEFORE_SAVE, $eventArgs);

            // create or update entity
            if ($isNew) {
                $affectedRows = $this->getRepository()->create($entity, false);
            } else {
                $affectedRows = $this->getRepository()->update($entity, null, false);
            }

            if (1 > $affectedRows) {
                throw new Exception\ServiceException('Error saving data');
            }

            // commit current transaction
            $this->trigger(self::ON_AFTER_SAVE, $eventArgs);
            $this->getRepository()->flush()->commit();

            if ($isNew) {
                $where = [];

                foreach ($this->getRepository()->pk as $v) {
                    $where[$v] = $entity->$v;
                }

                $criteria = ['where' => $where];
            }

            return $this->read($criteria);
        } catch (\Exception $ex) {
            $this->getRepository()->close()->rollback();
            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($criteria)
    {
        $result = $this->read($criteria);
        $entity = $result['data'];

        try {
            // start a transaction
            $eventArgs = new Event\UpdateEventArgs($criteria, $entity, false);

            if ($this->enableTransaction) {
                $this->getRepository()->beginTransaction();
            }

            $this->trigger(self::ON_BEFORE_DELETE, $eventArgs);

            // delete entity
            $affectedRows = $this->getRepository()->delete($entity, false);

            if (1 > $affectedRows) {
                throw new Exception\ServiceException('Error deleting data');
            }

            // commit current transaction
            $this->trigger(self::ON_AFTER_DELETE, $eventArgs);
            $this->getRepository()->flush()->commit();

            return ['success' => true];
        } catch (\Exception $ex) {
            $this->getRepository()->close()->rollback();
            throw $ex;
        }
    }

    // <editor-fold desc="MAGIC METHODS" defaultstate="collapsed">

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  mixed
     */
    public function __get($name)
    {
        return property_exists($this, $name) ? $this->$name : $this->getRepository()->$name;
    }

    // </editor-fold>
}
