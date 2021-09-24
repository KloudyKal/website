<?php
class AlternativeDuesPaymentApprovalStatus {
    const RECEIVED = 0;
    const REJECTED = 1;
    const APPROVED = 2;

    public static function get_name_from_value($x) {
        $semesterClass = new ReflectionClass ( 'AlternativeDuesPaymentApprovalStatus' );
        $constants = $semesterClass->getConstants();

        $constName = null;
        foreach ( $constants as $name => $value )
        {
            if ( $value == $x )
            {
                $constName = $name;
                break;
            }
        }

        return $constName;
    }
}