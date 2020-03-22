<?php

namespace App\Routes;

abstract class BaseRoute
{
    /**
     * @param $id
     * @return string
     */
    public abstract function index();

    /**
     * @return string
     */
    public abstract function create();

    /**
     * @param $id
     * @return string
     */
    public abstract function search($id);

    /**
     * @param $id
     * @return string
     */
    public abstract function update($id);

    /**
     * @param $id
     * @return string
     */
    public abstract function delete($id);

    /**
     * @param $inputs
     * @return array
     */
    protected abstract function inputsValidation($inputs);

    /**
     * @param $requiredFields
     * @param $inputs
     * @return array
     */
    protected function checkForMissingRequiredFields($requiredFields, $inputs)
    {
        $missingParameters = [];

        $missingRequiredKeys = array_diff_key(array_flip($requiredFields), $inputs);
        foreach ($missingRequiredKeys as $key => $value) {
            $missingParameters[$key] = [ 'message' => "Parameter '$key' is required" ];
        }

        return $missingParameters;
    }

    /**
     * @param $validFields
     * @param $inputs
     * @return array
     */
    protected function checkForInvalidFieldNames($validFields, $inputs)
    {
        $invalidParameters = [];

        $invalidKeys = array_diff_key($inputs, array_flip($validFields));
        foreach ($invalidKeys as $key => $value) {
            $invalidParameters[$key] = [ 'message' => "Invalid parameter '$key'" ];
        }

        return $invalidParameters;
    }
}