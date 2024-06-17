<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller
{
    protected $model;
    protected $validation;

    /**
     * Read all records from the database.
     *
     * @return array The response containing the message and data.
     */
    // !UNUSED
    // protected function read()
    // {
    //     $records = $this->model::all();
    //     return $records;
    // }

    /**
     * Read a record by its ID from the database.
     *
     * @param mixed $id The ID of the record.
     *
     * @return array The response containing the message and data.
     */
    protected function readById($id)
    {
        $record = $this->model::find($id);
        if ($record) {
            return $record;
        } else {
            return ['message' => 'Record not found'];
        }
    }

    /**
     * Read a record by a specific column and value from the database.
     *
     * @param string $column The column to search in.
     * @param mixed $value The value to search for.
     *
     * @return array The matching records.
     */
    protected function readByColumn(string $column, $value)
    {
        $records = $this->model::where($column, $value)->get();
        if ($records->isNotEmpty()) {
            return $records;
        } else {
            return collect(['message' => 'Record not found']);
        }
    }

    /**
     * Create a new record in the database.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return array The response containing data of the newly created record.
     */
    protected function create(Request $request)
    {
        $data = $request->except('file');

        $data['id'] = (string) Str::uuid();

        $record = $this->model::create($data);

        return $record;
    }


    /**
     * Update a record in the database.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param mixed $id The ID of the record.
     *
     * @return array The response containing data of the updated record.
     */
    protected function update(Request $request, $id)
    {
        $data = $request->except('file');

        $record = $this->model::find($id);
        if ($record) {
            $record->update($data);
            return $record;
        } else {
            return ['message' => 'Record not found'];
        }
    }

    /**
     * Delete a record from the database.
     *
     * @param mixed $id The ID of the record.
     *
     * @return array The response containing the message.
     */
    protected function delete($id)
    {
        $record = $this->model::find($id);
        if ($record) {
            $record->delete();
            return ['message' => 'Record deleted'];
        } else {
            return ['message' => 'Record not found'];
        }
    }

    /**
     * Create a record in the database in regular case.
     *
     * @param \Illuminate\Http\Request $req The request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the created record.
     */
    public function regular_create(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['create']);
        $record = $this->create($data);
        return $this->jsonResponse($record);
    }

    /**
     * Read a record by its ID from the database in regular case.
     *
     * @param \Illuminate\Http\Request $req The request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the read record.
     */
    public function regular_read_by_id(Request $req)
    {
        $data = $this->validateRequest($req, ['id' => ['required', 'uuid']]);
        $record = $this->readById($data['id']);
        return $this->jsonResponse($record);
    }

    /**
     * Update a record in the database in regular case.
     *
     * @param \Illuminate\Http\Request $req The request object.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated record.
     */
    public function regular_update(Request $req)
    {
        $data = $this->validateRequest($req, $this->validation['update']);
        $record = $this->update($data, $data['id']);
        return $this->jsonResponse($record);
    }

    /**
     * Delete a record from the database in regular case.
     *
     * @param \Illuminate\Http\Request $req The request object.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the message that the object is deleted.
     */
    public function regular_delete(Request $req)
    {
        $data = $this->validateRequest($req, ['id' => ['required', 'uuid']]);
        $record = $this->delete($data['id']);
        return $this->jsonResponse($record);
    }


    /**
     * Handle the file upload.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param string $inputName The name of the input field in the request. Defaults to 'file'.
     * @param string $disk The disk in which the file will be stored. Defaults to 'public'.
     *
     * @return string The URL of the uploaded file.
     */
    protected function uploadFile(Request $request, $inputName = 'file', $disk = 'public')
    {
        // Validate the request
        $request->validate([
            $inputName => 'required|file|mimes:jpg,jpeg,png,svg',
        ]);

        // Retrieve the file from the request
        $file = $request->file($inputName);

        // Store the file and get the stored filename
        $filename = $file->store('uploads', $disk);

        // Return the public URL
        return basename($filename);
    }

    /**
     * Delete a file from storage.
     *
     * @param string $file The name of the file to delete.
     * @param string $disk The disk in which the file is stored. Defaults to 'public'.
     *
     * @return bool Whether the file was successfully deleted.
     */
    protected function deleteFile($file, $disk = 'public')
    {
        $path = Storage::disk($disk)->path("uploads/$file");

        if (file_exists($path)) {
            return Storage::disk($disk)->delete("uploads/$file");
        }

        return false;
    }

    /**
     * Validate input and prevent overinput
     *
     * @param Request $request The response containing the request.
     * @param array $rules The rules to validate.
     *
     * @return \Illuminate\Http\Request
     */
    protected function validateRequest(Request $request, array $rules) {
        $request->only(array_keys($rules));
        $request->validate($rules);
        return $request;
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int   $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($data, $status = 200)
    {
        if (isset($data['status'])) {
            $status = $data['status'];
        }
        return response()->json($data, $status);
    }

}
