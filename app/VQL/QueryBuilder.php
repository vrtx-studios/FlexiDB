<?php

namespace App\VQL;

class QueryBuilder {

    protected $aParams = [];

    protected $iUserId = 0;

    public function setParams( $aParams = [] ) {
        $this->aParams = $aParams;
    }
    public function setUserId( $iUserId = 0 ) {
        $this->iUserId = $iUserId;
    }

    public function buildTableSQL() {
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        $aQuery = [];
        $aQuery[] = "CREATE TABLE $sTableName (";
        $aFields = $this->aParams['fields'];
        $aQueryFields = [];
        foreach( $aFields as $aField ) {
            $aQueryFields[] = $this->getFieldSQL( $aField['name'], $aField['type'], $aField );
        }
        $aQuery[] = implode( ", ", $aQueryFields );
        if( isset($this->aParams['key']) ) {
            $aQuery[] = ", PRIMARY KEY ( " . $this->aParams['key'] . " )";
        }
        $aQuery[] = ");";
        $sQuery = implode( " ", $aQuery );
        return $sQuery;
    }

    public function buildDropTableSQL() {
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        return "DROP TABLE $sTableName";
    }

    public function buildInsertSQL() {
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        $aQuery = [];
        $aQuery[] = "INSERT INTO $sTableName";
        $aColumns = [];
        foreach( $this->aParams['fields'] as $sField ) {
            $aColumns[] = $sField;
        }
        $aQuery[] = "(" . implode(", ", $aColumns) . ")";
        $aValues = [];
        foreach( $this->aParams['values'] as $sValue ) {
            $aValues[] = "'". current($sValue) . "'";
        }
        $aQuery[] = "VALUES (" . implode(", ", $aValues) . ");";
        return implode( " ", $aQuery );
    }

    public function buildReadSQL() {
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        $aQuery = [];
        $aQuery[] = "SELECT";
        $aColumns = [];
        foreach( $this->aParams['fields'] as $sField ) {
            $aColumns[] = $sField;
        }
        $aQuery[] = implode(", ", $aColumns);
        $aQuery[] = "FROM $sTableName";
        if( $this->aParams['value'] != "*" ) {
            $aQuery[] = "WHERE " . $this->aParams['selector'] . " = " . $this->aParams['value'];
        }
        return implode( " ", $aQuery );
    }

    public function buildUpdateSQL() {
        // UPDATE $sTableName SET col1=val1 WHERE con
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        $aQuery = [];
        $aQuery[] = "UPDATE $sTableName SET";
        $aValues = [];
        foreach( $this->aParams['values'] as $aValueSet ) {
            foreach( $aValueSet as $sKey => $sValue ) {
                if( !in_array( $sKey, $this->aParams['fields'] ) ) {
                    continue;
                }
                $aValues[] = $sKey . " = '" . $sValue . "'";
            }
        }
        $aQuery[] = implode( ", ", $aValues );
        $aQuery[] = "WHERE " . $this->aParams['selector'] . " = " . $this->aParams['value'];
        return implode( " ", $aQuery );
    }

    public function buildDeleteSQL() {
        $sTableName = "user_" . $this->iUserId . "_" . $this->aParams['table'];
        $aQuery[] = "DELETE FROM $sTableName";
        if( $this->aParams['value'] != "*" ) {
            $aQuery[] = "WHERE " . $this->aParams['selector'] . " = " . $this->aParams['value'];
        }
        return implode( " ", $aQuery );
    }

    private function getParameter( $sParam ) {
        if( isset($this->aParams[$sParam]) ) {
            return $this->aParams[$sParam];
        } else {
            return null;
        }
    }

    private function getFieldSQL( $sFieldName, $sFieldType, $aFieldParams = [] ) {
        switch( $sFieldType ) {
            case 'date':
            case 'time':
            case 'datetime':
            case 'timestamp':
                return $this->datetimeField( $sFieldName, $aFieldParams );
            case 'int':
            case 'integer':
                return $this->intField( $sFieldName, $aFieldParams );
            case 'double':
            case 'decimal':
                return $this->decimalField( $sFieldName, $aFieldParams );
            case 'enum':
            case 'array':
                return $this->enumField( $sFieldName, $aFieldParams );
                break;
            case 'string':
            case 'varchar':
            default:
                return $this->textField( $sFieldName, $aFieldParams );

        }
    }

    private function datetimeField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        $aReturn[] = "$sFieldName";
        if( isset($aFieldParams['precision']) && $aFieldParams['precision'] != "null" ) {
            $aReturn[] = "DATETIME(" . $aFieldParams['precision'] . ")";
        } else {
            $aReturn[] = "DATETIME";
        }
        if( isset($aFieldParams['default']) && strtolower($aFieldParams['default']) == "now" ) {
            $aReturn[] = "DEFAULT NOW()";
        }
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == false ) {
            $aReturn[] = "NOT NULL";
        }
        return implode( " ", $aReturn );
    }

    private function decimalField( $sFieldName, $aFieldParams ) {
        $aReturn = [];

        if( isset($aFieldParams['precision']) && $aFieldParams['precision'] != "null" ) {
            $aReturn[] = "$sFieldName DECIMAL(" . $aFieldParams['precision'] . ")";
        } else {
            $aReturn[] = "$sFieldName DECIMAL PRECISION";
        }
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == false ) {
            $aReturn[] = "NOT NULL";
        }
        return implode( " ", $aReturn );
    }

    private function enumField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        if( !isset($aFieldParams['values']) || empty($aFieldParams['values']) ) {
            return response()->json( [
                'message' => 'arrays must have pre-defined values'
            ], 400 );
        }

        $aReturn[] = "$sFieldName ENUM";

        $sValues = "(";
        $aValues = [];
        foreach($aFieldParams['values'] as $value ) {
            $aValues[] = "'$value'";
        }
        $sValues .= implode(",", $aValues ) . ")";
        $aReturn[] = $sValues;

        if( isset($aFieldParams['default']) ) {
            $sDefault = $aFieldParams['default'];
            $aReturn[] = "DEFAULT '" . $sDefault . "'";
        }

        return implode( " ", $aReturn );

    }

    private function textField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        if( isset($aFieldParams['length']) && $aFieldParams['length'] > 1 ) {
            $aReturn[] = "$sFieldName VARCHAR(" . $aFieldParams['length'] . ")";
        } else {
            $aReturn[] = "$sFieldName VARCHAR(100)";
        }
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == false ) {
            $aReturn[] = "NOT NULL";
        }
        return implode( " ", $aReturn );
    }

    private function intField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        $aReturn[] = "$sFieldName INT";
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == false ) {
            $aReturn[] = "NOT NULL";
        }
        if( isset($aFieldParams['autoinc']) && $aFieldParams['autoinc'] == true ) {
            $aReturn[] = "AUTO_INCREMENT";
        }
        return implode( " ", $aReturn );
    }

}
