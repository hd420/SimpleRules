<?php

namespace SimpleRules;

use Exceptions\SimpleRulesException;

/**
 * Class - Simple rules processor.
 *
 */
class SimpleRules
{

    /**
     * The memory. a key-value array.
     *
     * @var array
     */
    public $memory;

    /**
     * The rules being evaluated.
     *
     * @var string
     */
    public $strRules;

    /**
     * A collection of tokens.
     *
     * @var array
     */
    public $tokens;


    /**
     * The token being evaluated.
     *
     * @param string $token
     *
     * @return void
     */
    public function eval($token)
    {
        $token = 'fn' . $token;

        return $this->$token();
    }

    /**
     * Rule Function - add
     *
     * Definition - add key expression
     *
     * @return void
     */
    public function fnadd()
    {
        $key = $this->getNextToken();

        $v0 = $this->memory[$key];
        $v1 = $this->getNextTokenValue();

        $this->memory[$key] = $v0 + $v1;
    }

    /**
     * Rule Function - eq
     *
     * Definition - eq key expression
     *
     * @return void
     */
    public function fneq()
    {
        $v0 = $this->getNextTokenValue();
        $v1 = $this->getNextTokenValue();

        return $v0 == $v1;
    }

    /**
     * Rule Function - gt
     *
     * Definition - gt key expression
     *
     * @return void
     */
    public function fngt()
    {
        $v0 = $this->getNextTokenValue();
        $v1 = $this->getNextTokenValue();

        return $v0 > $v1;
    }

    /**
     * Rule Function - if
     *
     * Definition - if condition... then function... fi
     *
     * @return void
     */
    public function fnif()
    {
        $token = $this->getNextToken();

        while (strcmp($token, 'then') != 0) {
            $result = $this->eval($token);

            if (!$result) {
                $this->skipTo('fi');
                break;
            }

            $token = $this->getNextToken();
        }

        if ($result) {
            $token = $this->getNextToken();

            while (strcmp($token, 'fi') != 0) {
                $this->eval($token);
                $token = $this->getNextToken();
            }
        }
    }

    /**
     * Rule Function - lt
     *
     * Definition - lt key expression
     *
     * @return void
     */
    public function fnlt()
    {
        $v0 = $this->getNextTokenValue();
        $v1 = $this->getNextTokenValue();

        return $v0 < $v1;
    }

    /**
     * Rule Function - push
     *
     * Definition - push key expression
     *
     * @return void
     */
    public function fnpush()
    {
        $key = $this->getNextToken();
        $value = $this->getNextTokenValue();

        $this->memory[$key][] = $value;
    }

    /**
     * Rule Function - requires
     *
     * Definition - requires key
     *
     * @return void
     */
    public function fnrequires()
    {
        $memoryName = $this->getNextToken();

        if (!array_key_exists($memoryName, $this->memory)) {
            throw new SimpleRulesException('requires ' . $memoryName);
        }
    }

    /**
     * Rule Function - result
     *
     * Definition - result expression
     *
     * @return void
     */
    public function fnresult()
    {
        throw new SimpleRulesException($this->getNextTokenValue());
    }

    /**
     * Rule Function - set
     *
     * Definition - set key expression
     *
     * @return void
     */
    public function fnset()
    {
        $key = $this->getNextToken();
        $value = $this->getNextTokenValue();

        $this->memory[$key] = $value;
    }

    /**
     * Rule Function - sub
     *
     * Definition - sub key expression
     *
     * @return void
     */
    public function fnsub()
    {
        $key = $this->getNextToken();

        $v0 = $this->memory[$key];
        $v1 = $this->getNextTokenValue();

        $this->memory[$key] = $v0 - $v1;
    }

    /**
     * Return the next token.
     *
     * @return void
     */
    public function getNextToken()
    {
        $token = array_shift($this->tokens)[0];

        return $token;
    }

    /**
     * Returns the value of the token
     *
     * @return mixed The value of the token
     */
    public function getNextTokenValue()
    {
        $token = $this->getNextToken();

        if (is_numeric($token)) {
            return $token;
        } elseif ($token[0] == '"') {
            return trim($token, '"');
        } else {
            return $this->memory[$token];
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function parse()
    {
        $result = true;

        try {
            $re = '/[a-z0-9\.]+|[0-9]+|".+"/mi';

            preg_match_all($re, $this->strRules, $this->tokens, PREG_SET_ORDER, 0);

            while (count($this->tokens) > 0) {
                $token = $this->getNextToken();

                $this->eval($token);
            }
        } catch (SimpleRulesException $e) {
        }

        return $this->memory['messages'];
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function peek()
    {
        return $this->tokens[0][0];
    }

    /**
     * Undocumented function
     *
     * @param string $token the token being evaluated
     *
     * @return void
     */
    public function skipTo($token)
    {
        while (strcmp($this->getNextToken(true), $token) != 0);
    }
}
