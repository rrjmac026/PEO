<?php

namespace App\Enums;

enum Role: string {
    case ADMIN = 'admin';
    case CONTRACTOR = 'contractor';
    case PROVINCIAL_ENGINEER = 'provincial_engineer';
    case SITE_INSPECTOR = 'site_inspector';
    case ENGINEER_IV = 'engineeriv';
    case SURVEYOR = 'surveyor';
    case MTQA = 'mtqa';
    case RESIDENT_ENGINEER = 'resident_engineer';
    case ENGINEER_III = 'engineeriii';
}