<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleDocumentMap extends Model
{

    protected $table =
    'ngdrstab_mst_article_document_mapping';


    protected $primaryKey =
    'article_doc_map_id';


    public $timestamps = false;



    public function document()
    {
        return $this->belongsTo(
            UploadDocument::class,
            'document_id',
            'document_id'
        );
    }
}
