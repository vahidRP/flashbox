<?php

namespace App\Support\Repository\Contracts;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\Paginator;

interface RepositoryInterface
{
    /**
     * Finds one item by the provided field.
     *
     * @param mixed       $value   mixed Value used for the filter. If NULL passed then it will take ONLY the criteria.
     * @param string|null $field   Field on the database that you will filter by. Default: id.
     * @param array       $columns columns to retrieve with the object
     * @return mixed|JsonResponse model|NULL An Eloquent object when there is a result,
     *                             NULL when there are no matches
     */
    public function findOneBy($value = null, ?string $field = null, array $columns = ['*']);

    /**
     * Finds one item.
     *
     * @param array  $columns     columns to retrieve with the object
     * @return mixed|JsonResponse model|NULL An Eloquent object when there is a result,
     *                            NULL when there are no matches
     */
    public function findOne(array $columns = ['*']);

    /**
     * Finds ALL items the repository abstract without any kind of filter.
     *
     * @param array $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAll(array $columns = ['*']);

    /**
     * Count ALL items the repository abstract without any kind of filter.
     *
     * @return JsonResponse
     */
    public function countAll(array $columns = ['*']);

    /**
     * Finds ALL items by the provided field. If NULL specified for the first 2 parameters, then it will take ONLY the
     * criteria.
     *
     * @param mixed       $value   mixed Value used for the filter
     * @param string|null $field   Field on the database that you will filter by. Default: id.
     * @param array       $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAllBy($value = null, ?string $field = null, array $columns = ['*']);

    /**
     * Finds ALL the items in the repository where the given field is inside the given values.
     *
     * @param array  $value   mixed Array of values used for the filter
     * @param string $field   field on the database that you will filter by
     * @param array  $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAllWhereIn(array $value, $field, array $columns = ['*']);

    /**
     * Allows you to eager-load entity relationships when retrieving entities, either with or without criterias.
     *
     * @param array|string $relations relations to eager-load along with the entities
     * @return $this the current repository object instance
     */
    public function with($relations);

    /**
     * Adds a criteria to the query.
     *
     * @param CriteriaInterface $criteria object that declares and implements the criteria used
     * @return $this the current repository object instance
     */
    public function addCriteria(CriteriaInterface $criteria);

    /**
     * Map collection of data before passing to respondWithResource.
     *
     * @param MapperInterface $mapper object that declares and implements the mapper used
     * @return $this the current repository object instance
     */
    public function addMapper(MapperInterface $mapper);

    /**
     * Skips the current criteria (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|true $status if you want to skip the criteria or not
     * @return $this the current repository object instance
     */
    public function skipCriteria($status = true);

    /**
     * Set the data transformer.
     *
     * @param string $resource The resource class name
     * @return $this the current repository object instance
     */
    public function setResource($resource);

    /**
     * Set the collection transformer.
     *
     * @param string $resource The resource class name
     * @return $this the current repository object instance
     */
    public function setCollectionResource($resource);

    /**
     * Skips the current resource (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|true $status if you want to skip the criteria or not
     * @return $this the current repository object instance
     */
    public function skipResource($status = true);

    /**
     * Returns model className string.
     */
    public function getModelClassName();

    /**
     * Cast data value
     *
     * @param array $data
     */
    public function cast(array $data);

    /**
     * Set validations.
     *
     * @param array $rules
     * @param array $messages
     * @return $this the current repository object instance
     */
    public function setValidator(array $rules, array $messages = []);

    /**
     * validates data.
     *
     * @param array $data
     * @return $this the current repository object instance
     */
    public function validate(array $data);

    /**
     * Return a JsonResponse.
     *
     * @param mixed      $result
     * @param int|string $httpCode
     * @return mixed
     */
    public function respondWithResource($result, $httpCode = Response::HTTP_OK);

    /**
     * Returns a Paginator that based on the criteria or filters given.
     *
     * @param int   $perPage number of results to return per page
     * @param array $columns columns to retrieve with the objects
     * @return Paginator|JsonResponse object with the results and the paginator
     */
    public function paginate($perPage, array $columns = ['*']);

    /**
     * Allows you to set the current page with using the paginator. Useful when you want to overwrite the $_GET['page']
     * parameter and retrieve a specific page directly without using HTTP.
     *
     * @param int $page the page you want to retrieve
     * @return $this the current repository object instance
     */
    public function setCurrentPage($page);

    /**
     * Creates a new entity of the entity type the repository handles, given certain data.
     *
     * @param array $data data the entity will have
     * @return mixed model|NULL An Eloquent object when the entity was created, NULL in case of error
     */
    public function create(array $data);

    /**
     * Updates as many entities as the filter matches with the given $data.
     *
     * @param array       $data  fields & new values to be updated on the entity/entities
     * @param mixed       $value mixed Value used for the filter
     * @param string|null $field Field on the database that you will filter by. Default: id.
     * @param array       $columns
     * @return mixed model|NULL|integer An Eloquent object representing the updated entity, a number of entities
     *                           updated if mass updating, or NULL in case of error
     */
    public function updateBy(array $data, $value = null, ?string $field = null, array $columns = ['*']);

    /**
     * Removes as many entities as the filter matches. If softdelete is applied, then they will be soft-deleted.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param mixed       $value mixed Value used for the filter
     * @param string|null $field Field on the database that you will filter by. Default: id.
     * @return bool TRUE It will always return TRUE
     * @throws \Exception
     */
    public function delete($value = null, ?string $field = null);

    /**
     * @return int number of records matching the criteria (or total amount of records)
     */
    public function count();

    /**
     * Resets the current scope of the repository. That is: clean the criteria, and all other properties that could have
     * been modified, like current page, etc.
     *
     * @return $this the current repository object instance
     */
    public function resetScope();

    /**
     * Permanently removes a record (or set of records) from the database.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param mixed       $value mixed Value used for the filter
     * @param string|null $field field on the database that you will filter by
     * @return mixed
     */
    public function destroy($value = null, ?string $field = null);

    /**
     * Removes/Unset fields that are not fillable.
     *
     * @param array $data
     * @return array
     */
    public function cleanUnfillableFields(array $data);

    /**
     * Returns model instance.
     */
    public function getModel();
}
