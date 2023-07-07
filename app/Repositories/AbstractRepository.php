<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class AbstractRepository
{
    protected $model;

    protected $modelName;

    /**
     * AbstractRepository constructor.
     */
    public function __construct()
    {
        $this->modelName = get_class($this->model);
    }

    /**
     * Accoding pk to serach.
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Accoding column to serach.
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return Model
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        return $this->model
            ->where($attribute, $value)
            ->first($columns);
    }

    /**
     * Find a row or add a data.
     * @param array $data
     * @return Model
     */
    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    /**
     * Get all data.
     * @param array $columns
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * Pagination showing of data.
     * @param int $perPage
     * @param array $columns
     * @return Collection
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Add a data of model.
     * @param array $data
     * @return Model
     */
    public function new(array $data)
    {
        return new $this->modelName($data);
    }

    /**
     * Add new data. (many)
     * @param String    $target:處理目標名稱
     * @param array     $data
     * @return array
     */
    public function createMany(String $target, array $data) : array
    {
        $code       = config('code.apiSuccess');
        $message    = '';
        $model      = null;
        DB::connection()->beginTransaction();
        try {
            $model = $this->model->insert($data);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('create-many-' . $target . '-error:' . $e->getMessage());
            $code    = config('code.apiExceptionError');
            $message = $target . '大量新增出錯';
        }

        return [
            'code'    => $code,
            'message' => $message,
            'model'   => $model
        ];
    }

    /**
     * Add new data. (one)
     * @param string    $target:處理目標名稱
     * @param array     $data
     * @return array
     */
    public function create(string $target, array $data) : array
    {
        $code       = config('code.apiSuccess');
        $message    = '';
        DB::connection()->beginTransaction();
        try {
            $model = $this->model->create($data);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('create-' . $target . '-error:' . $e->getMessage());
            $code    = config('code.apiExceptionError');
            $message = $target . '單一新增出錯';
        }

        return [
            'model'   => $model,
            'code'    => $code,
            'message' => $message,
        ];
    }

    /**
     * Update data.
     * @param String    $target:處理目標名稱
     * @param array     $data
     * @param $id
     * @param string    $attribute
     * @return array
     */
    public function update(String $target, array $data, $id, $attribute = 'id') : array
    {
        $code       = config('code.apiSuccess');
        $message    = '';
        DB::connection()->beginTransaction();
        try {
            $this->model
            ->where($attribute, '=', $id)
            ->update($data);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('update-' . $target . '-error:' . $e->getMessage());
            $code    = config('code.apiExceptionError');
            $message = $target . '修改出錯';
        }

        return [
            'code'    => $code,
            'message' => $message
        ];
    }

    /**
     * Update data by Ids.
     * @param String    $target:處理目標名稱
     * @param array     $data
     * @param $ids
     * @param string    $attribute
     * @return array
     */
    public function updateByIds(String $target, array $data, $ids) : array
    {
        $code       = config('code.apiSuccess');
        $message    = '';
        DB::connection()->beginTransaction();
        try {
            $this->model
            ->whereIn('id', $ids)
            ->update($data);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('update-' . $target . '-error:' . $e->getMessage());
            $code    = config('code.apiExceptionError');
            $message = $target . '修改出錯';
        }

        return [
            'code'    => $code,
            'message' => $message
        ];
    }

    /**
     * Remove data.
     * @param string  $target:處理目標名稱
     * @param int     $id
     * @return array
     */
    public function remove($target, $id)
    {
        $now        = date('Y-m-d H:i:s');
        $code       = config('code.apiSuccess');
        $message    = '';
        $getOne     = $this->model->where('id', $id)->first();
        if (!$getOne) {
            $code    = config('code.apiNotFoundError');
            $message = $target . '刪除出錯-找不到目標';
        }

        DB::connection()->beginTransaction();
        try {
            $this->model
            ->where('id', $id)
            ->update([
                'status'     => 0,
                'deleted_at' => $now
            ]);
            DB::connection()->commit();
        } catch (\Exception $e) {
            DB::connection()->rollback();
            Log::error('remove-' . $target . '-error:' . $e->getMessage());
            $code    = config('code.apiExceptionError');
            $message = $target . '刪除出錯';
        }

        return [
            'code'    => $code,
            'message' => $message
        ];
    }

    /**
     * Deleta data.
     * @param $id
     * @return Model
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Count data row.
     * @return int
     */
    public function counts() : int
    {
        return $this->model->count();
    }

    public function createTest(array $data) : object
    {
        return $this->model->create($data);
    }
}
