<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Controller
{
    protected $model;
    protected $validationRules = [];

    public function read()
    {
        $records = $this->model::all();
        return $this->jsonResponse(['message' => 'Records found', 'data' => $records]);
    }

    public function readById($id)
    {
        $record = $this->model::find($id);
        if ($record) {
            return $this->jsonResponse(['message' => 'Record found', 'data' => $record]);
        } else {
            return $this->jsonResponse(['message' => 'Record not found'], 404);
        }
    }

    public function create(Request $request)
    {
        $request->validate($this->validationRules['create']);

        $data = $request->all();

        $data['id'] = (string) Str::uuid();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/' . Str::plural(strtolower(class_basename($this->model))));
            $data['file'] = basename($path);
        }

        $record = $this->model::create($data);

        return $this->jsonResponse(['message' => 'Record created', 'data' => $record], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate($this->validationRules['update']);

        $data = $request->all();
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/' . Str::plural(strtolower(class_basename($this->model))));
            $data['file'] = basename($path);
        }

        $record = $this->model::find($id);
        if ($record) {
            $record->update($data);
            return $this->jsonResponse(['message' => 'Record updated', 'data' => $record]);
        } else {
            return $this->jsonResponse(['message' => 'Record not found'], 404);
        }
    }

    public function delete($id)
    {
        $record = $this->model::find($id);
        if ($record) {
            $record->delete();
            return $this->jsonResponse(['message' => 'Record deleted']);
        } else {
            return $this->jsonResponse(['message' => 'Record not found'], 404);
        }
    }

    protected function jsonResponse($data, $status = 200)
    {
        return response()->json($data, $status);
    }
}
