<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadDocument extends Model
{
    protected $table =
    'ngdrstab_mst_upload_document';


    protected $primaryKey =
    'document_id';


    public $timestamps = false;



    public function mapping()
    {
        return $this->hasMany(
            ArticleDocumentMap::class,
            'document_id',
            'document_id'
        );
    }
}
