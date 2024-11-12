<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class CheckUser implements ValidationRule
{
    protected $projectId;
    protected $method;

    public function __construct($projectId, $method)
    {
        $this->projectId = $projectId;
        $this->method = $method;
    }

    // Check if user is a developer in the project
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {// for updata task
        if ($this->method === 'PUT') {
            $task = Task::find($this->projectId);
            if ($task) {
                $this->projectId = $task->project_id;
            }
        }
        //
        $exists = DB::table('project_user')
            ->where('user_id', $value)
            ->where('project_id', $this->projectId)
            ->where('role', 'Developer')
            ->exists();

        if (!$exists) {
            $fail('تحقق من المستخدم انه من اعضاء الفريق وان يكن مطور');
        }
    }
}
