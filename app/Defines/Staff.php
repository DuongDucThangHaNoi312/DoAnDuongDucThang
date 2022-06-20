<?php

namespace App\Defines;

class Staff
{
    const GENDER_FEMALE = 0;
    const GENDER_MALE   = 1;

    const STATUS_QUIT           = 0;
    const STATUS_PROBATIONARY   = 1;
    const STATUS_OFFICIAL       = 2;

    const WEIGHT_NV = 1;
    const WEIGHT_PP = 2;
    const WEIGHT_TP = 3;
    const WEIGHT_KT = 4;
    const WEIGHT_GD = 5;
    const WEIGHT_TGD= 6;

    const MARITAL_STATUS_SINGLE     = 0;
    const MARITAL_STATUS_MARRIED    = 1;

    const QUALIFICATION_POST_GRADUATE = 'POST_GRADUATE';
    const QUALIFICATION_UNIVERSITY    = 'UNIVERSITY';
    const QUALIFICATION_COLLEGE       = 'COLLEGE';
    const QUALIFICATION_INTERMEDIATE  = 'INTERMEDIATE';
    const QUALIFICATION_HIGHSCHOOL    = 'HIGHSCHOOL';
    const QUALIFICATION_SECONDARY     = 'SECONDARY';

    const DRIVER_LICENSE_CLASS_B1 = "B1";
    const DRIVER_LICENSE_CLASS_B2 = "B2";
    const DRIVER_LICENSE_CLASS_C = "C";
    const DRIVER_LICENSE_CLASS_D = "D";
    const DRIVER_LICENSE_CLASS_E = "E";
    const DRIVER_LICENSE_CLASS_FB2 = "FB2";
    const DRIVER_LICENSE_CLASS_FC = "FC";
    const DRIVER_LICENSE_CLASS_FD = "FD";
    const DRIVER_LICENSE_CLASS_FE = "FE";

    const FAMILY_RELATIONSHIP_WIFE      = "WI";
    const FAMILY_RELATIONSHIP_HUSBAND   = "HU";
    const FAMILY_RELATIONSHIP_CHILDREN  = "CH";
    const FAMILY_RELATIONSHIP_MOTHER    = "MO";
    const FAMILY_RELATIONSHIP_FATHER    = "FA";
    const FAMILY_RELATIONSHIP_BROTHER   = "BR";
    const FAMILY_RELATIONSHIP_SISTER    = "SI";
    const FAMILY_RELATIONSHIP_YOUNGER   = "YO";
    const USER_EXCEPT = ['System', 'Administrator', 'KT LOG', 'KT PAC', 'Admin'];

    const ROLE_CODE_NV = 'NV';

    const ROLE_NV = "9";

    public static function getQualificationsForOption()
    {
        return [
            self::QUALIFICATION_UNIVERSITY      => trans('staffs.qualifications.' . self::QUALIFICATION_UNIVERSITY),
            self::QUALIFICATION_COLLEGE         => trans('staffs.qualifications.' . self::QUALIFICATION_COLLEGE),
            self::QUALIFICATION_INTERMEDIATE    => trans('staffs.qualifications.' . self::QUALIFICATION_INTERMEDIATE),
            self::QUALIFICATION_POST_GRADUATE   => trans('staffs.qualifications.' . self::QUALIFICATION_POST_GRADUATE),
            self::QUALIFICATION_HIGHSCHOOL      => trans('staffs.qualifications.' . self::QUALIFICATION_HIGHSCHOOL),
            self::QUALIFICATION_SECONDARY       => trans('staffs.qualifications.' . self::QUALIFICATION_SECONDARY),
        ];
    }

    public static function getFamilyRelationshipsForOption()
    {
        return [
            self::FAMILY_RELATIONSHIP_WIFE     => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_WIFE),
            self::FAMILY_RELATIONSHIP_HUSBAND  => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_HUSBAND),
            self::FAMILY_RELATIONSHIP_CHILDREN => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_CHILDREN),
            self::FAMILY_RELATIONSHIP_MOTHER   => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_MOTHER),
            self::FAMILY_RELATIONSHIP_FATHER   => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_FATHER),
            self::FAMILY_RELATIONSHIP_BROTHER  => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_BROTHER),
            self::FAMILY_RELATIONSHIP_SISTER   => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_SISTER),
            self::FAMILY_RELATIONSHIP_YOUNGER  => trans('staffs.family_relationships.' . self::FAMILY_RELATIONSHIP_YOUNGER),
        ];
    }

    public static function getDriverLicensesForOption()
    {
        return [ self::DRIVER_LICENSE_CLASS_B1 => self::DRIVER_LICENSE_CLASS_B1, self::DRIVER_LICENSE_CLASS_B2 => self::DRIVER_LICENSE_CLASS_B2, self::DRIVER_LICENSE_CLASS_C => self::DRIVER_LICENSE_CLASS_C, self::DRIVER_LICENSE_CLASS_D => self::DRIVER_LICENSE_CLASS_D, self::DRIVER_LICENSE_CLASS_E => self::DRIVER_LICENSE_CLASS_E, self::DRIVER_LICENSE_CLASS_FB2 => self::DRIVER_LICENSE_CLASS_FB2, self::DRIVER_LICENSE_CLASS_FC => self::DRIVER_LICENSE_CLASS_FC, self::DRIVER_LICENSE_CLASS_FD => self::DRIVER_LICENSE_CLASS_FD, self::DRIVER_LICENSE_CLASS_FE => self::DRIVER_LICENSE_CLASS_FE ];
    }


    public static function getGendersForOption()
    {
        return [ self::GENDER_FEMALE => trans('staffs.genders.' . self::GENDER_FEMALE), self::GENDER_MALE => trans('staffs.genders.' . self::GENDER_MALE) ];
    }

    public static function getMaritalStatusForOption()
    {
        return [ self::MARITAL_STATUS_SINGLE => trans('staffs.marital_status.' . self::MARITAL_STATUS_SINGLE), self::MARITAL_STATUS_MARRIED => trans('staffs.marital_status.' . self::MARITAL_STATUS_MARRIED) ];
    }

    public static function getStatusForOption()
    {
        return [
            self::STATUS_QUIT => trans('staffs.status.' . self::STATUS_QUIT),
            self::STATUS_PROBATIONARY => trans('staffs.status.' . self::STATUS_PROBATIONARY),
            self::STATUS_OFFICIAL => trans('staffs.status.' . self::STATUS_OFFICIAL)];
    }

    public static function getWeightForOption()
    {
        return
            [
                self::WEIGHT_NV => trans('staff_titles.weights.' . self::WEIGHT_NV),
                self::WEIGHT_PP => trans('staff_titles.weights.' . self::WEIGHT_PP),
                self::WEIGHT_TP => trans('staff_titles.weights.' . self::WEIGHT_TP),
                self::WEIGHT_KT => trans('staff_titles.weights.' . self::WEIGHT_KT),
                self::WEIGHT_GD => trans('staff_titles.weights.' . self::WEIGHT_GD),
                self::WEIGHT_TGD => trans('staff_titles.weights.' . self::WEIGHT_TGD)
            ];
    }

    public static function getColorStatus($level)
    {
        switch ($level) {
            case self::STATUS_QUIT:
                return "<span class='btn bg-red btn-flat margin'>" . trans('staffs.status.' . self::STATUS_QUIT) . "</span>";
            case self::STATUS_PROBATIONARY:
                return "<span class='btn bg-orange btn-flat margin'>" . trans('staffs.status.' . self::STATUS_PROBATIONARY) . "</span>";
            case self::STATUS_OFFICIAL:
                return "<span class='btn bg-green btn-flat margin'>" . trans('staffs.status.' . self::STATUS_OFFICIAL) . "</span>";
        }
    }

    public static function getStatusForOptionContract()
	{
		return [
			self::STATUS_PROBATIONARY => trans('staffs.status.' . self::STATUS_PROBATIONARY),
			self::STATUS_OFFICIAL => trans('staffs.status.' . self::STATUS_OFFICIAL)];
	}
}