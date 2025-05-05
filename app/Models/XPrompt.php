<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\XGeneratedPost;

class XPrompt extends Model
{
    use HasFactory;

    // テーブル名の指定
    protected $table = 'x_prompts';

    // 主キーの指定
    protected $primaryKey = 'prompt_id';

    // 主キーのインクリメントを有効にする（デフォルトで有効）
    public $incrementing = true;

    // 主キーの型の指定
    protected $keyType = 'int';

    // マスアサインメント可能なフィールドの指定
    protected $fillable = [
        'prompt_text',
        'created_at'
    ];
    
    /**
     * リレーション：生成ポスト (x_generated_posts テーブル)
     */
    public function generatedPosts(): HasMany
    {
        return $this->hasMany(
            XGeneratedPost::class,
            'prompt_id',    // XGeneratedPost 側の FK
            'prompt_id'     // XPrompt の PK
        );
    }
}
