<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Absence;

class AbsenceFormRequest extends FormRequest
{
    public function authorize()
    {
        // Check if the user has permission to create or update absences
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * This method defines the validation rules for the absence request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'worker_id' => 'required|exists:workers,id', // The worker must exist in the database
            'day' => 'required|integer|min:1|max:31', // Day must be a number between 1 and 31
            'month' => 'required|integer|min:1|max:12', // Month must be a number between 1 and 12
            'year' => 'required|integer|min:2020|max:2099', // Year must be within a valid range (from 2020 to 2099)
            'reason' => 'required|string|max:255', // The reason must be a string not exceeding 255 characters
            'minutes' => 'required|integer|min:1|max:1440', // Minutes must be between 1 and 1440 (one full day)
            'with_consent' => 'nullable|boolean', // Whether the absence has consent, can be a boolean value
        ];
    }

    public function prepareForValidation()
    {
        // If 'date' is provided, use the setDateAttribute method to set the day, month, and year
        if ($this->has('date')) {
            $absence = new Absence();
            $absence->setDateAttribute($this->date); // Use the setDateAttribute to set day, month, and year
    
            // Merge the day, month, and year into the request data
            $this->merge([
                'day' => $absence->day,
                'month' => $absence->month,
                'year' => $absence->year,
            ]);
        }
    }
}
