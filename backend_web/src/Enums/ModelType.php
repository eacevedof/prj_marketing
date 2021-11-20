<?php

namespace App\Enums;

abstract class ModelType
{
    const STRING = "string";
    const INT = "int";
    const DATETIME = "datetime";
    const DATE = "date";
    const DECIMAL = "decimal";

    const INSERT_USER = "insert_user";
    const INSERT_DATE = "insert_date";
    const INSERT_PLATFORM = "insert_platform";

    const UPDATE_USER = "update_user";
    const UPDATE_DATE = "update_date";
    const UPDATE_PLATFORM = "update_platform";

    const DELETE_USER = "delete_user";
    const DELETE_DATE = "delete_date";
    const DELETE_PLATFORM = "delete_platform";

    const REQUEST_KEY = "requestkey";
}
