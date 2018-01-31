<?php
/*
 * This file is part of the Yosymfony\ParserUtils package.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Yosymfony\ParserUtils;

/** Tokens should have a numeric id, becahse comparison of numeric token ids and storage
 *  space are optimal, even in interpreted languages like php, where I expect strings will be referenced
 *  counted.  I don't think php integer values are reference counted.
 *  For debugging messages, use a lookup table to get string names for ids
 *  Unfortunately some other class must provide the id to name conversion.
 *  
 */
class Token
{
    protected $value;
    protected $id;
    protected $line;

    /**
     * Constructor.
     *
     * @param string $value The value of the token
     * @param int $id The constant id of the token. e.g: T_BRAKET_BEGIN
     * @param int $line Line of the code in where the token is found.
     *
     */
    public function __construct(string $value, int $id, int $line)
    {
        $this->value = $value;
        $this->id = $id;
        $this->line = $line;
    }

    /**
     * Returns the value (the match term)
     *
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Returns the name of the token
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Returns the line of the code in where the token is found
     *
     * @return int
     */
    public function getLine() : int
    {
        return $this->line;
    }

    public function __toString() : string
    {
        return sprintf(
            "[\n id: %s\n value:%s\n line: %s\n]",
            $this->id,
            $this->value,
            $this->line
        );
    }
}
