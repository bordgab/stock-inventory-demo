<?php

namespace App\Model;

enum SemiconductorPackage: string
{
    case TO_3 = 'TO-3';
    case DIP = 'DIP';
    case DIL = 'DIL';
}
