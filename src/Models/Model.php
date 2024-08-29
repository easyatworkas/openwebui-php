<?php

namespace EasyAtWork\OpenWebUi\Models;

use JsonSerializable;
use EasyAtWork\OpenWebUi\Traits\StaticConstructor;

abstract class Model implements JsonSerializable
{
    use StaticConstructor;
}
