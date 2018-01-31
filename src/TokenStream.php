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

use Yosymfony\Toml\Lexer;

class TokenStream
{
    protected $tokens;
    protected $index = -1;

    /**
     * Constructor
     *
     * @param Token[] List of tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * This can't be implemented by using other access methods
     * Therefore its a fundamental operation.
       Does not change parser internal state
     */
    
    public function peekNext() : int
    {
        $test = $this->tokens[$this->index+1] ?? null;
        return (is_null($test) ? 0 : $test->getId());
    }
    
    /**
     * Moves the pointer one token forward
     *
     * @return Token|null The token or null if there are not more tokens
     */
    public function moveNext() : ?Token
    {
        return $this->tokens[++$this->index] ?? null;
    }

    /**
     * Matches the next token. This method moves the pointer one token forward
     * if an error does not occur
     *
     * @param string $tokenName The name of the token
     *
     * @return string The value of the token
     *
     * @throws SyntaxErrorException If the next token does not match
     */
    public function matchNext(int $tokenId) : string
    {
        $token = $this->moveNext();
        --$this->index;

        if ($token->getId() == $tokenId) {
            return $this->moveNext()->getValue();
        }

        throw new SyntaxErrorException(sprintf(
            'Syntax error: expected token with name "%s" instead of "%s" at line %s.',
            Lexer::tokenName($tokenId),
            Lexer::tokenName($token->getId()),
            $token->getLine()));
    }

    /**
     * Skips tokens while they match with the token name passed as argument.
     * This method moves the pointer "n" tokens forward until the last one
     * that match with the token name
     *
     * @param int $tokenId The name of the token
     * @param int $maxCount If $maxCount > 0, limits number of possible skips.
     * @return number of tokens skipped
     */
    public function skipWhile(int $tokenId, int $maxCount=0) : int
    {
        $skipped = 0;
        while($this->peekNext() === $tokenId) {
            $this->index++;
            $skipped++;
            if (($maxCount > 0) && ($skipped >= $maxCount))
                break;
        }
        return $skipped;
    }

    /**
     * Skips tokens while they match with one of the token names passed as
     * argument. This method moves the pointer "n" tokens forward until the
     * last one that match with one of the token names
     *
     * @param string[] $tokenNames List of token names
     */
    public function skipWhileAny(array $tokenIds) : void
    {
        while ($this->isNextAny($tokenIds)) {
            $this->index++;
        }
    }

    /**
     * Checks if the next token matches with the token name passed as argument
     *
     * @param string $tokenName The name of the token
     *
     * @return bool
     */
    public function isNext(int $tokenId) : bool
    {
       return $this->peekNext() === $tokenId;
    }

    /**
     * Checks if the following tokens in the stream match with the sequence of tokens
     *
     * @param int[] $tokenIds Sequence of token ids
     *
     * @return bool
     */
    public function isNextSequence(array $tokenIds) : bool
    {
        $result = true;
        $currentIndex = $this->index;

        foreach ($tokenIds as $id) {
            $token = $this->moveNext();

            if ($token === null || $token->getId() != $id) {
                $result = false;

                break;
            }
        }

        $this->index = $currentIndex;

        return $result;
    }

    /**
     * Checks if one of the tokens passed as argument is the next token
     *
     * @param int[] $tokenIds List of token names. e.g: 'T_PLUS', 'T_SUB'
     *
     * @return bool
     */
    public function isNextAny(array $tokenIds) : bool
    {
        $test = $this->peekNext();
        
        foreach ($tokenIds as $id) {
            if ($id === $test) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all tokens
     *
     * @return token[] List of tokens
     */
    public function getAll() : array
    {
        return $this->tokens;
    }

    /**
     * Has pending tokens?
     *
     * @return bool
     */
    public function hasPendingTokens() :bool
    {
        $tokenCount = count($this->tokens);

        if ($tokenCount == 0) {
            return false;
        }

        return $this->index < ($tokenCount - 1);
    }

    /**
     * Resets the stream to the beginning
     */
    public function reset() : void
    {
        $this->index = -1;
    }
}
