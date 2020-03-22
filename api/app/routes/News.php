<?php

use App\Core\Helpers\RequestMethod;
use App\Core\Helpers\StringExtensions;
use App\Core\Log;
use App\Models\News as NewsModel;
use App\Routes\BaseRoute;
use App\Core\Helpers\ResponseFormat;
use App\Core\Response;

class News extends BaseRoute
{
    private $log;
    private $newsModel;

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->log = Log::getInstance(env('LOG_PATH'));
        $this->newsModel = new NewsModel();
    }

    /**
     * @param $id
     * @return string
     */
    public function index()
    {
        try {
            $response = [];
            if ($_SERVER['REQUEST_METHOD'] == RequestMethod::GET) {
                $response = $this->newsModel->get();
                if (!isset($response) || empty($response)) {
                    $response = [ 'message' => 'No results found' ];
                }
            }

            return Response::response($response);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @return string
     */
    public function create()
    {
        try {
            $response = [];

            if ($_SERVER['REQUEST_METHOD'] == RequestMethod::POST) {
                if (isset($_POST) && !empty($_POST) && isset($_POST[0])) {
                    return Response::response([ 'message' => 'Bulk Insert is NOT supported' ], ResponseFormat::JSON);
                }

                $requiredParametersNames = [ 'title', 'date', 'text' ];
                $response = $this->checkForMissingRequiredFields($requiredParametersNames, $_POST);
                $response += $this->checkForInvalidFieldNames($requiredParametersNames, $_POST);
                $response += $this->inputsValidation($_POST);
                if (!isset($response) || empty($response)) {
                    $recordId = $this->newsModel->create($_POST);
                    if ($recordId > 0) {
                        $response = $this->newsModel->get([ 'id' => $recordId ]);
                    } else {
                        $response = [ 'message' => 'Internal server error.' ];
                        $this->log->warning($_POST);
                    }
                }
            }

            return Response::response($response);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function search($id)
    {
        try {
            $response = [];
            if ($_SERVER['REQUEST_METHOD'] == RequestMethod::GET) {
                $params = [ 'id' => $id ];
                $response = $this->newsModel->get($params);
                if (!isset($response) || empty($response)) {
                    $response = [ 'message' => 'No results found' ];
                }
            }

            return Response::response($response);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function update($id)
    {
        try {
            $response = [];

            if ($_SERVER['REQUEST_METHOD'] == RequestMethod::POST) {
                if (isset($_POST) && !empty($_POST) && isset($_POST[0])) {
                    return Response::response([ 'message' => 'Bulk Update is NOT supported' ], ResponseFormat::JSON);
                }

                $params = [ 'id' => $id ] + $_POST;
                $validFieldNames = [ 'id', 'title', 'date', 'text' ];
                $response = $this->checkForInvalidFieldNames($validFieldNames, $params);
                $response += $this->inputsValidation($params);
                if (!isset($response) || empty($response)) {
                    $recordId = $this->newsModel->update($params);
                    if ($recordId > 0) {
                        $response = [ 'message' => 'Record was updated successfully' ];
                    } else {
                        $response = [ 'message' => 'Nothing to update' ];
                    }
                }
            }

            return Response::response($response);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function delete($id)
    {
        try {
            $response = [];

            if ($_SERVER['REQUEST_METHOD'] == RequestMethod::DELETE) {
                $params = [ 'id' => $id ];
                $response = $this->inputsValidation($params);
                if (empty($response)) {
                    $deletedRows = $this->newsModel->delete($params);
                    if ($deletedRows > 0) {
                        $response['message'] = 'Record was deleted successfully';
                    } else {
                        $response['message'] = 'Record not exists';
                    }
                }
            }

            return Response::response($response);
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }

    /**
     * @param array $inputs
     * @return array
     */
    protected function inputsValidation($inputs)
    {
        try {
            $errors = [];

            foreach ($inputs as $key => $value) {
                $value = trim($value);
                switch ($key) {
                    case 'id':
                        $isIdEmpty = StringExtensions::isEmpty($value);
                        if ($isIdEmpty && $value != 0) {
                            $errors[$key] = [ 'message' => 'Field cannot be empty' ];
                        }

                        if (!isset($errors[$key]) && !is_numeric($value)) {
                            $errors[$key] = "Invalid value for argument 'id'";
                        }
                        break;
                    case 'title':
                        $isTitleEmpty = StringExtensions::isEmpty($value);
                        if ($isTitleEmpty) {
                            $errors[$key] = [ 'message' => 'Field cannot be empty' ];
                        }

                        $titleMaxLength = 255;
                        if (!isset($errors[$key]) && strlen($value) > $titleMaxLength) {
                            $errors[$key] = [ 'message' => "Title is too long. Max allowed length is $titleMaxLength characters" ];
                        }

                        $news = $this->newsModel->get([ 'title' => $value ]);
                        if (!isset($errors[$key]) && isset($news) && !empty($news) && isset($news[0]['title'])) {
                            $errors[$key] = [ 'message' => 'Title already exists' ];
                        }
                        break;
                    case 'date':
                        $isDateEmpty = StringExtensions::isEmpty($value);
                        if ($isDateEmpty) {
                            $errors[$key] = [ 'message' => 'Field cannot be empty' ];
                        }

                        $isDateValid = StringExtensions::isValidDate($value);
                        if (!isset($errors[$key]) && !$isDateValid) {
                            $errors[$key] = [ 'message' => 'Date format is not valid' ];
                        }
                        break;
                    case 'text':
                        $isTextEmpty = StringExtensions::isEmpty($value);
                        if ($isTextEmpty) {
                            $errors[$key] = [ 'message' => 'Field cannot be empty' ];
                        }

                        $textMaxLength = 2000;
                        if (!isset($errors[$key]) && strlen($value) > $textMaxLength) {
                            $errors[$key] = [ 'message' => "Text is too long. Max allowed length is $textMaxLength characters" ];
                        }
                        break;
                    default:
                        $errors[$key] = [ 'message' => "Invalid parameter '$key'" ];
                        break;
                }
            }

            return $errors;
        } catch (\Exception $ex) {
            $this->log->error($ex->getMessage() . PHP_EOL . $ex->getTraceAsString());
        }
    }
}