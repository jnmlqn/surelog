<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProjectSchedules implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $data)
    {
    	$validated = true;
        foreach ($data as $key => $d) {
        	if (empty($d['time_in'])) {
        		$validated = false;
        	} else if (empty($d['time_out'])) {
        		$validated = false;
        	}
        }
        return $validated;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The time in/time out cannot be null.';
    }
}
