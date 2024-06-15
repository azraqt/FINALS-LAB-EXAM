<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
class MedicalRecordController extends Controller

{
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,user_id',
            'doctor_id' => 'required|exists:doctors,doctor_id',
            'visit_date' => 'required|date',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $record = new MedicalRecord([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'visit_date' => $request->visit_date,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'notes' => $request->notes,
        ]);

        $record->save();

        return response()->json(['message' => 'Medical record added successfully'], 201);
    }

    public function index($patient_id)
    {
        // Retrieve the logged-in patient
        $patient = Patient::where('id', $patient_id)->first(); 
        // Check if patient exists
        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // Fetch medical records associated with the patient
        $medicalRecords = MedicalRecord::where('patient_id', $patient_id)
        ->with('doctor:doctor_id,first_name,last_name') // Eager load doctor information
        ->get();

    return response()->json(['medical_records' => $medicalRecords]);
    }

    public function doctorView($patient_id)
    {
        // Fetch medical records for the specified patient
        $medicalRecords = MedicalRecord::where('patient_id', $patient_id)
            ->with('doctor') // Load related doctor information
            ->orderBy('visit_date', 'desc') // Optionally, order by visit date
            ->get();

        return response()->json(['medical_records' => $medicalRecords]);
    }
}
