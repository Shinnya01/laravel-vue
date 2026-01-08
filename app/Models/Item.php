<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        "title","description","file_name", "folder_id", "user_id"
    ];

    public function folder(){
        return $this->BelongsTo(Folder::class);
    }

    public function user(){
        return $this->BelongsTo(User::class);
    }
}
