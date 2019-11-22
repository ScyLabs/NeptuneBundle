<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 04/11/2019
 * Time: 14:25
 */

namespace ScyLabs\NeptuneBundle\Annotation\ScyLabsNeptune;


/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *  @Attribute("key",type="string"),
 *  @Attribute("classNameSpace",type="string"),
 * })
 */
final class Override
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $class;

}