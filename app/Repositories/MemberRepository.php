<?php
namespace App\Repositories;

use App\Repositories\AbstractRepository;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberRepository extends AbstractRepository
{
    /** @var Member $model Model */
    protected $model;

    /**
     * Repository constructor.
     *
     * @param Member $model
     */
    public function __construct(Member $model)
    {
        $this->model     = $model;
        parent::__construct();
    }

    /**
    * 取得單一
    * @param  int $memberId
    * @return object
    */
    public function findOne($memberId)
    {
        return $this->model->where('id', $memberId)
            ->where('status', config('setting.on'))
            ->first();
    }

     /**
     * Remove Member By Self.
     * @param string  $target:處理目標名稱
     * @param int     $id
     * @return array
     */
    public function removeMemberBySelf($target, $id)
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
            $removeArray = [
                'account'      => 'del_' . time() . '_' . $getOne->account,
                'status'       => config('setting.off'),
                'deleted_at'   => $now
            ];
            $this->model
                ->where('id', $id)
                ->update($removeArray);
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
}
