<?php

namespace App\Support\Repository;

use App\Helpers\Helpers;
use App\Support\Repository\Contracts\CriteriaInterface;
use App\Support\Repository\Contracts\MapperInterface;
use App\Support\Repository\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * @var string|array
     */
    protected $with;

    /**
     * @var bool
     */
    protected $skipCriteria;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $skipResource;

    /**
     * @var string
     */
    protected $collectionResource;

    /**
     * @var array
     */
    protected $vRules;

    /**
     * @var array
     */
    protected $vMessages = [];

    /**
     * @var array
     */
    protected $vCustomAttributes = [];

    /**
     * @var array
     */
    protected $criteria;

    /**
     * @var array
     */
    protected $mapper;

    /**
     * @var string
     */
    private $modelClassName;

    /**
     * EloquentRepository constructor.
     *
     * @param Model|\LdapRecord\Models\Model $model
     */
    public function __construct(Model|\LdapRecord\Models\Model $model)
    {
        $this->model = $model;

        // A clean copy of the model is needed when the scope needs to be reset.
        $reflex = new ReflectionClass($model);
        $this->modelClassName = $reflex->getName();

        $this->skipCriteria = false;
        $this->skipResource = false;
        $this->criteria = [];
        $this->mapper = [];
    }

    /**
     * Set the data transformer.
     *
     * @param string $resource The resource class name
     * @return $this the current repository object instance
     */
    public function setResource($resource)
    {
        if(is_object($resource)){
            $resource = get_class($resource);
        }

        $this->resource = $resource;

        return $this;
    }

    /**
     * Set the data transformer.
     *
     * @param string $resource The resource class name
     * @return $this the current repository object instance
     */
    public function setCollectionResource($resource)
    {
        if(is_object($resource)){
            $resource = get_class($resource);
        }

        $this->collectionResource = $resource;

        return $this;
    }

    /**
     * Returns model className string.
     */
    public function getModelClassName()
    {
        return $this->modelClassName;
    }

    /**
     * Returns model instance.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set validations.
     *
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return $this the current repository object instance
     */
    public function setValidator(array $rules, array $messages = [], array $customAttributes = [])
    {
        $this->vRules = $rules;
        $this->vMessages = $messages;
        $this->vCustomAttributes = $customAttributes;

        return $this;
    }

    /**
     * Finds one item by the provided field.
     *
     * @param mixed  $value       mixed Value used for the filter. If NULL passed then it will take ONLY the criteria.
     * @param string $field       Field on the database that you will filter by. Default: id.
     * @param array  $columns     columns to retrieve with the object
     * @return mixed|JsonResponse model|NULL An Eloquent object when there is a result,
     *                            NULL when there are no matches
     */
    public function findOneBy($value = null, $field = null, array $columns = ['*'])
    {
        $field = empty($field) ? $this->model->getKeyName() : $field;
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->where($field, $value)->firstOrFail($columns);

        $response = $this->respondWithResource($this->applyMapper($result));
        $this->resetScope();

        return $response;
    }

    /**
     * Finds one item.
     *
     * @param array $columns      columns to retrieve with the object
     * @return mixed|JsonResponse model|NULL An Eloquent object when there is a result,
     *                            NULL when there are no matches
     */
    public function findOne(array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->first($columns);

        $this->resetScope();

        return $this->respondWithResource($this->applyMapper($result));
    }

    /**
     * Eager Load Relations.
     */
    protected function eagerLoadRelations()
    {
        if(is_array($this->with)){
            $this->model = $this->model->with($this->with);
        }
    }

    /**
     * Eager Load Relations On Model.
     */
    protected function eagerLoadRelationsOnModel(&$model)
    {
        if(is_array($this->with)){
            $model = $model->load($this->with);
        }
    }

    /**
     * Apply Criteria.
     *
     * @return $this
     */
    private function applyCriteria()
    {
        if(!$this->skipCriteria){
            foreach($this->criteria as $criteria){
                if($criteria instanceof CriteriaInterface){
                    $this->model = $criteria->apply($this->model);
                }
            }
        }

        return $this;
    }

    /**
     * Apply Mapper.
     *
     * @param $result
     * @return mixed $result
     */
    private function applyMapper($result)
    {
        foreach($this->mapper as $mapper){
            if($mapper instanceof MapperInterface){
                $result = $mapper->apply($result);
            }
        }

        return $result;
    }

    /**
     * Resets the current scope of the repository. That is: clean the criteria, and all other properties that could have
     * been modified, like current page, etc.
     *
     * @return $this the current repository object instance
     */
    public function resetScope()
    {
        $this->vRules = null;
        $this->vMessages = [];
        $this->vCustomAttributes = [];
        $this->criteria = [];
        $this->skipCriteria(false);
        $this->skipResource(false);
        $this->model = new $this->modelClassName();

        if($this->model->getConnectionName() === 'crate'){
            DB::connection('crate')->statement(('REFRESH TABLE ' . $this->model->getTable()));
        }

        return $this;
    }

    /**
     * Skips the current criteria (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|true $status if you want to skip the criteria or not
     * @return $this the current repository object instance
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Skips the current resource (all of them). Useful when you don't want to reset the object but just not use the
     * filters applied so far.
     *
     * @param bool|true $status if you want to skip the criteria or not
     * @return $this the current repository object instance
     */
    public function skipResource($status = true)
    {
        $this->skipResource = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function respondWithResource($result, $httpCode = Response::HTTP_OK)
    {
        if(!$this->skipResource && $result){
            $additional = [];
            if(is_array($result) && isset($result['data'])){
                $data = $result['data'];
                unset($result['data']);
                $additional = $result;
                $result = $data;
            }

            if($result instanceof AbstractPaginator || $result instanceof Collection || $result instanceof \LdapRecord\Models\Collection){
                if($this->collectionResource){
                    $resource = new $this->collectionResource($result);
                    $result = $resource->additional($additional)->response()->setStatusCode($httpCode);
                }
            }else if($this->resource){
                $resource = new $this->resource($result);
                $result = $resource->additional($additional)->response()->setStatusCode($httpCode);
            }
        }

        return $result;
    }

    /**
     * Finds ALL items by the provided field. If NULL specified for the first 2 parameters, then it will take ONLY the
     * criteria.
     *
     * @param mixed       $value   mixed Value used for the filter
     * @param string|null $field   Field on the database that you will filter by. Default: id.
     * @param array       $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAllBy($value = null, $field = null, array $columns = ['*'])
    {
        $field = empty($field) ? $this->model->getKeyName() : $field;
        $this->eagerLoadRelations();
        $this->applyCriteria();

        if(!is_null($value) && !is_null($field)){
            $this->model = $this->model->where($field, $value);
        }

        $result = $this->model->get($columns);

        $response = $this->respondWithResource($this->applyMapper($result));
        $this->resetScope();

        return $response;
    }

    /**
     * Finds ALL the items in the repository where the given field is inside the given values.
     *
     * @param array  $value   mixed Array of values used for the filter
     * @param string $field   field on the database that you will filter by
     * @param array  $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAllWhereIn(array $value, $field, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->whereIn($field, $value)->get($columns);

        $response = $this->respondWithResource($this->applyMapper($result));
        $this->resetScope();

        return $response;
    }

    /**
     * Finds ALL items the repository abstract without any kind of filter.
     *
     * @param array $columns columns to retrieve with the objects
     * @return mixed|JsonResponse collection Laravel Eloquent's Collection that may or may not be empty
     */
    public function findAll(array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->get($columns);

        $response = $this->respondWithResource($this->applyMapper($result));
        $this->resetScope();

        return $response;
    }

    public function countAll(array $columns = ['*'])
    {
        $this->applyCriteria();

        $result = $this->model->count($columns);

        $this->skipResource();

        $response = $this->respondWithResource($this->applyMapper([
            'data'=>[
                'count'=> $result
            ]
        ]));
        $this->resetScope();

        return $response;

    }

    /**
     * Allows you to eager-load entity relationships when retrieving entities, either with or without criterias.
     *
     * @param array|string $relations relations to eager-load along with the entities
     * @return $this the current repository object instance
     */
    public function with($relations)
    {
        if(is_string($relations)){
            $relations = func_get_args();
        }

        $this->with = $relations;

        return $this;
    }

    /**
     * Adds a criteria to the query.
     *
     * @param CriteriaInterface $criteria object that declares and implements the criteria used
     * @return $this the current repository object instance
     */
    public function addCriteria(CriteriaInterface $criteria)
    {
        $this->criteria[] = $criteria;

        return $this;
    }

    /**
     * Map collection of data before passing to respondWithResource.
     *
     * @param MapperInterface $mapper object that declares and implements the mapper used
     * @return $this the current repository object instance
     */
    public function addMapper(MapperInterface $mapper)
    {
        $this->mapper[] = $mapper;

        return $this;
    }

    /**
     * Returns a Paginator that based on the criteria or filters given.
     *
     * @param int   $perPage number of results to return per page
     * @param array $columns columns to retrieve with the objects
     * @return Paginator|JsonResponse object with the results and the paginator
     */
    public function paginate($perPage, array $columns = ['*'])
    {
        $this->eagerLoadRelations();
        $this->applyCriteria();

        $result = $this->model->paginate($perPage, $columns);
        if(method_exists($result, 'appends')){
            $result->appends(app('request')->query());
        }

        $response = $this->respondWithResource($this->applyMapper($result));
        $this->resetScope();

        return $response;
    }

    /**
     * Allows you to set the current page with using the paginator. Useful when you want to overwrite the $_GET['page']
     * parameter and retrieve a specific page directly without using HTTP.
     *
     * @param int $page the page you want to retrieve
     * @return $this the current repository object instance
     */
    public function setCurrentPage($page)
    {
        Paginator::currentPageResolver(function() use ($page){
            return $page;
        });

        return $this;
    }

    /**
     * Creates a new entity of the entity type the repository handles, given certain data.
     *
     * @param array $data data the entity will have
     * @return mixed model|NULL An Eloquent object when the entity was created, NULL in case of error
     * @throws ValidationException
     */
    public function create(array $data)
    {
        $data = $this->cast($data);
        $this->validate($data);
        $cleanFields = $this->cleanUnfillableFields($data);

        $createdObject = $this->model->create($cleanFields);

        $response = $this->applyMapper($createdObject);
        $this->resetScope();

        return $response;
    }

    /**
     * Removes/Unset fields that are not fillable.
     *
     * @param array $data
     * @return array
     */
    public function cleanUnfillableFields(array $data)
    {
        $fillableFields = $this->model->getFillable();

        foreach($data as $key => $value){
            if(!in_array($key, $fillableFields)){
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Validate.
     *
     * @param array $data
     * @return EloquentRepository
     * @throws ValidationException
     */
    public function validate(array $data)
    {
        if(!empty($this->vRules)){
            $validator = Validator::make($data, $this->vRules, $this->vMessages, $this->vCustomAttributes);

            if($validator->fails()){
                throw new ValidationException($validator, new JsonResponse($validator->errors()
                    ->getMessages(), Response::HTTP_UNPROCESSABLE_ENTITY));
            }
        }

        return $this;
    }

    /**
     * Updates as many entities as the filter matches with the given $data.
     *
     * @param array  $data  fields & new values to be updated on the entity/entities
     * @param mixed  $value mixed Value used for the filter
     * @param string $field Field on the database that you will filter by. Default: id.
     * @param array  $columns
     * @return mixed model|NULL|integer An Eloquent object representing the updated entity, a number of entities
     *                      updated if mass updating, or NULL in case of error
     * @throws ValidationException
     */
    public function updateBy(array $data, $value = null, $field = null, array $columns = ['*'])
    {
        $field = empty($field) ? $this->model->getKeyName() : $field;
        $data = $this->cast($data);
        $this->validate($data);

        $cleanFields = $this->cleanUnfillableFields($data);

        if(!is_null($value)){
            // Single update.
            $model = $this->model->where($field, $value)->first($columns);
            $model->update($cleanFields);
            $model->refresh();

            $this->eagerLoadRelationsOnModel($model);
            $returnedVal = $model;
        }else{
            // Mass update.
            $this->applyCriteria();
            $this->eagerLoadRelations();

            $models = $this->model->get($columns);
            foreach($models as $model){
                $model->update($cleanFields);
                $model->refresh();
            }

            $this->eagerLoadRelationsOnModel($models);
            $returnedVal = $models;
        }

        $response = $this->respondWithResource($this->applyMapper($returnedVal));
        $this->resetScope();

        return $response;
    }

    /**
     * Removes as many entities as the filter matches. If softdelete is applied, then they will be soft-deleted.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param mixed  $value mixed Value used for the filter
     * @param string $field Field on the database that you will filter by. Default: id.
     * @return bool TRUE It will always return TRUE
     * @throws \Exception
     */
    public function delete($value = null, $field = null)
    {
        $field = empty($field) ? $this->model->getKeyName() : $field;
        $this->applyCriteria();
        $result = false;

        if(!is_null($value)){
            if($model = $this->model->where($field, $value)->first(['*', $field])){
                $result = $model->delete();
            }
        }else{
            if(!empty($this->criteria)){
                $models = $this->model->get(['*', $field]);
                foreach($models as $model){
                    $result = $model->delete();
                }
            }else{
                $result = false;
            }
        }

        $this->resetScope();

        return (bool)$result;
    }

    /**
     * @return int number of records matching the criteria (or total amount of records)
     */
    public function count()
    {
        $this->applyCriteria();
        $response = $this->model->count();

        $this->resetScope();

        return $response;
    }

    /**
     * Permanently removes a record (or set of records) from the database.
     * Criteria is applied as well, so please be careful with it.
     *
     * @param mixed  $value mixed Value used for the filter
     * @param string $field field on the database that you will filter by
     * @return mixed
     */
    public function destroy($value = null, $field = null)
    {
        $field = empty($field) ? $this->model->getKeyName() : $field;
        $this->applyCriteria();

        if(!is_null($value)){
            $result = $this->model->where($field, $value)->forceDelete();
        }else{
            if(!empty($this->criteria)){
                $result = $this->model->forceDelete();
            }else{
                $result = false;
            }
        }

        $this->resetScope();

        return (bool)$result;
    }

    /**
     * @param array $data
     * @return array
     */
    public function cast(array $data): array
    {
        foreach($data as $column => $value){
            if(!($value instanceof UploadedFile)){
                $data[$column] = Helpers::normalize($value, $column);
                if($column === 'password'){
                    if(empty($data[$column])){
                        unset($data[$column]);
                    }else{
                        $data[$column] = Hash::make($data[$column]);
                    }
                }

                foreach($this->model->getCasts() as $field => $cast){
                    if($cast === 'array' && gettype($value) !== 'array' && $column === $field){
                        $value = json_decode($value, true);
                        $data[$column] = $value;
                    }
                }
            }
        }

        return $data;
    }
}
