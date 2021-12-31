<?php


namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class BaseRepository extends ServiceEntityRepository
{
    private $entityClass;

    /**
     * BaseRepository constructor.
     *
     * @param  ManagerRegistry  $registry
     * @param  null  $entityClass
     */
    public function __construct(ManagerRegistry $registry, $entityClass = null)
    {
        $this->entityClass = $entityClass;
        parent::__construct($registry, $entityClass);
    }

    /**
     * @return null
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param $collection
     *
     * @return RepositoryResponse
     */
    public function insert($collection): RepositoryResponse
    {
        try {
            if (!is_object($collection)) {
                $entity = new $this->entityClass;
                foreach ($collection as $k => $v) {
                    $setter = 'set'.ucfirst($k);
                    if (method_exists($entity, $setter)) {
                        $entity->{$setter}($v);
                    }
                }
            } else {
                $entity = $collection;
            }
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (\Exception $e) {
            return new RepositoryResponse(null, false, $e->getMessage(), $e);
        }

        return new RepositoryResponse($entity);
    }

    /**
     * @param $entity
     * @param  array  $collection
     *
     * @return RepositoryResponse
     */
    public function update($entity, array $collection): RepositoryResponse
    {
        try {
            foreach ($collection as $k => $v) {
                $setter = 'set'.ucfirst($k);
                if (method_exists($entity, $setter)) {
                    $entity->{$setter}($v);
                }
            }

            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (\Exception $e) {
            return new RepositoryResponse(null, false, $e->getMessage(), $e);
        }

        return new RepositoryResponse($entity);
    }

    public function findOneWithCriteria(array $criterias): RepositoryResponse
    {
        try {
            $repo = $this->_em->getRepository($this->_entityName);
            $result = $repo->findOneBy($criterias);
        } catch (\Exception $e) {
            return new RepositoryResponse(null, false, $e->getMessage(), $e);
        }

        return new RepositoryResponse($result);
    }

    public function findAll()
    {
     return new RepositoryResponse(parent::findAll());
    }

    public function returnRepositoryResponse(Paginator $paginator): RepositoryResponse
    {
        $repositoryResponse = new RepositoryResponse();

        try {
            $result = $paginator->getQuery()->getResult();
        }catch (\Exception $e) {
            $repositoryResponse->setSuccess(false);
            $repositoryResponse->setMessage($e->getMessage());

            return $repositoryResponse;
        }

        return $repositoryResponse
            ->setTotalRow($paginator->count())
            ->setResponse($result);
    }
}
