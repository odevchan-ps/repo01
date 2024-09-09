<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTimeline extends Model
{
    use HasFactory;

    protected $table = 'task_timelines';

    // 更新可能なカラムを指定
    protected $fillable = [
        'task_name', 'status', 'start_time', 'end_time', 'duration', 'error_message',
        'affected_ids', 'record_count', 'additional_info'  // これらを追加
    ];

    // タイムスタンプの自動管理をONにする
    public $timestamps = true;
}
