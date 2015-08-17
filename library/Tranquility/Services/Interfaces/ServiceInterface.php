<?php namespace Tranquility\Services\Interfaces;

interface ServiceInterface {
	// Data access functions
	public function all();
	public function paginate($perPage = 20);
	public function create(array $data);
	public function update($id, array $data);
	public function delete($id);
	public function find($id);
	public function findBy($fieldName, $fieldValue);
}