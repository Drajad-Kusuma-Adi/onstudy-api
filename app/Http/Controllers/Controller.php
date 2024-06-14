<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller
{
    protected $model;

    /**
     * Read all records from the database.
     *
     * @return array The response containing the message and data.
     */
    protected function read()
    {
        $records = $this->model::all();
        return ['message' => 'Records found', 'data' => $records];
    }

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
            return ['message' => 'Record found', 'data' => $record];
        } else {
            return ['message' => 'Record not found'];
        }
    }

    /**
     * Create a new record in the database.
     *
     * @param \Illuminate\Http\Request $request The request object.
     *
     * @return array The response containing the message and data.
     */
    protected function create(Request $request)
    {
        $data = $request->except('file');

        $data['id'] = (string) Str::uuid();

        $record = $this->model::create($data);

        return ['message' => 'Record created', 'data' => $record];
    }


    /**
     * Update a record in the database.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param mixed $id The ID of the record.
     *
     * @return array The response containing the message and data.
     */
    protected function update(Request $request, $id)
    {
        $data = $request->except('file');

        $record = $this->model::find($id);
        if ($record) {
            $record->update($data);
            return ['message' => 'Record updated', 'data' => $record];
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

    // TODO: function to delete file

    /**
     * Validate input and prevent overinput
     *
     * @param Request $request The response containing the request.
     * @param array $rules The rules to validate.
     * @param array $acceptables The acceptable values.
     *
     * @return \Illuminate\Http\Request
    */
    protected function validateRequest(Request $request, array $rules, array $acceptables) {
        $request->only($acceptables);
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
