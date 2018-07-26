<?php
namespace example\Models;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Example.
 *
 * @property int example_id
 * @property string name
 * @property string url
 */
class Site extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'example_id', 'name',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];
    public static $rules = [
        'name' => 'required|max:64',
    ];
    public function exampleExcludes()
    {
        return $this->hasMany("example\Models\ExampleExclude");
    }
    public function exampleRequires()
    {
        return $this->hasMany("example\Models\ExampleRequire");
    }
}
