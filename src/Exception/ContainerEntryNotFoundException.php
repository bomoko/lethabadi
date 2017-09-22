<?php
/**
 * Created by PhpStorm.
 * User: bomoko
 * Date: 2017/09/23
 * Time: 2:15 AM
 */

namespace Bomoko\Lethaba\Exception;
use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{

}