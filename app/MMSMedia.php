<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MMSMedia extends Model
{
    protected $hidden = ['media'];
    protected $fillable = ['mediaSid', 'MessageSid', 'mediaUrl', 'media', 'filename', 'MIMEType'];
}
