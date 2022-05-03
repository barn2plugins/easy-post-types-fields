<?php

/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\WordPressCS\WordPress\Sniffs\CodeAnalysis;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\WordPressCS\WordPress\Sniff;
use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\PHP_CodeSniffer\Util\Tokens;
/**
 * Detects variable assignments being made within conditions.
 *
 * This is a typical code smell and more often than not a comparison was intended.
 *
 * Note: this sniff does not detect variable assignments in ternaries without parentheses!
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal This sniff is a duplicate of the same sniff as pulled upstream.
 * Once the upstream sniff has been merged and the minimum WPCS PHPCS requirement has gone up to
 * the version in which the sniff was merged, this version can be safely removed.
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1594} }}
 */
class AssignmentInConditionSniff extends Sniff
{
    /**
     * Assignment tokens to trigger on.
     *
     * Set in the register() method.
     *
     * @since 0.14.0
     *
     * @var array
     */
    protected $assignment_tokens = array();
    /**
     * The tokens that indicate the start of a condition.
     *
     * @since 0.14.0
     *
     * @var array
     */
    protected $condition_start_tokens = array();
    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @since 0.14.0
     *
     * @return array
     */
    public function register()
    {
        $this->assignment_tokens = Tokens::$assignmentTokens;
        unset($this->assignment_tokens[\T_DOUBLE_ARROW]);
        $starters = Tokens::$booleanOperators;
        $starters[\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_SEMICOLON] = \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_SEMICOLON;
        $starters[\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_OPEN_PARENTHESIS] = \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_OPEN_PARENTHESIS;
        $starters[\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_INLINE_ELSE] = \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_INLINE_ELSE;
        $this->condition_start_tokens = $starters;
        return array(\T_IF, \T_ELSEIF, \T_FOR, \T_SWITCH, \T_CASE, \T_WHILE, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_INLINE_THEN);
    }
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 0.14.0
     *
     * @param int $stackPtr The position of the current token in the stack.
     *
     * @return void
     */
    public function process_token($stackPtr)
    {
        $token = $this->tokens[$stackPtr];
        // Find the condition opener/closer.
        if (\T_FOR === $token['code']) {
            if (isset($token['parenthesis_opener'], $token['parenthesis_closer']) === \false) {
                return;
            }
            $semicolon = $this->phpcsFile->findNext(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_SEMICOLON, $token['parenthesis_opener'] + 1, $token['parenthesis_closer']);
            if (\false === $semicolon) {
                return;
            }
            $opener = $semicolon;
            $semicolon = $this->phpcsFile->findNext(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_SEMICOLON, $opener + 1, $token['parenthesis_closer']);
            if (\false === $semicolon) {
                return;
            }
            $closer = $semicolon;
            unset($semicolon);
        } elseif (\T_CASE === $token['code']) {
            if (isset($token['scope_opener']) === \false) {
                return;
            }
            $opener = $stackPtr;
            $closer = $token['scope_opener'];
        } elseif (\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_INLINE_THEN === $token['code']) {
            // Check if the condition for the ternary is bracketed.
            $prev = $this->phpcsFile->findPrevious(Tokens::$emptyTokens, $stackPtr - 1, null, \true);
            if (\false === $prev) {
                // Shouldn't happen, but in that case we don't have anything to examine anyway.
                return;
            }
            if (\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_CLOSE_PARENTHESIS === $this->tokens[$prev]['code']) {
                if (!isset($this->tokens[$prev]['parenthesis_opener'])) {
                    return;
                }
                $opener = $this->tokens[$prev]['parenthesis_opener'];
                $closer = $prev;
            } elseif (isset($token['nested_parenthesis'])) {
                $closer = \end($token['nested_parenthesis']);
                $opener = \key($token['nested_parenthesis']);
                $next_statement_closer = $this->phpcsFile->findEndOfStatement($stackPtr, array(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_COLON, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_CLOSE_PARENTHESIS, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_CLOSE_SQUARE_BRACKET));
                if (\false !== $next_statement_closer && $next_statement_closer < $closer) {
                    // Parentheses are unrelated to the ternary.
                    return;
                }
                $prev_statement_closer = $this->phpcsFile->findStartOfStatement($stackPtr, array(\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_COLON, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_OPEN_PARENTHESIS, \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_OPEN_SQUARE_BRACKET));
                if (\false !== $prev_statement_closer && $opener < $prev_statement_closer) {
                    // Parentheses are unrelated to the ternary.
                    return;
                }
                if ($closer > $stackPtr) {
                    $closer = $stackPtr;
                }
            } else {
                // No parenthesis found, can't determine where the conditional part of the ternary starts.
                return;
            }
        } else {
            if (isset($token['parenthesis_opener'], $token['parenthesis_closer']) === \false) {
                return;
            }
            $opener = $token['parenthesis_opener'];
            $closer = $token['parenthesis_closer'];
        }
        $startPos = $opener;
        do {
            $hasAssignment = $this->phpcsFile->findNext($this->assignment_tokens, $startPos + 1, $closer);
            if (\false === $hasAssignment) {
                return;
            }
            // Examine whether the left side is a variable.
            $hasVariable = \false;
            $conditionStart = $startPos;
            $altConditionStart = $this->phpcsFile->findPrevious($this->condition_start_tokens, $hasAssignment - 1, $startPos);
            if (\false !== $altConditionStart) {
                $conditionStart = $altConditionStart;
            }
            for ($i = $hasAssignment; $i > $conditionStart; $i--) {
                if (isset(Tokens::$emptyTokens[$this->tokens[$i]['code']])) {
                    continue;
                }
                // If this is a variable or array, we've seen all we need to see.
                if (\T_VARIABLE === $this->tokens[$i]['code'] || \Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_CLOSE_SQUARE_BRACKET === $this->tokens[$i]['code']) {
                    $hasVariable = \true;
                    break;
                }
                // If this is a function call or something, we are OK.
                if (\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_CLOSE_PARENTHESIS === $this->tokens[$i]['code']) {
                    break;
                }
            }
            if (\true === $hasVariable) {
                $errorCode = 'Found';
                if (\T_WHILE === $token['code']) {
                    $errorCode = 'FoundInWhileCondition';
                } elseif (\Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\T_INLINE_THEN === $token['code']) {
                    $errorCode = 'FoundInTernaryCondition';
                }
                $this->phpcsFile->addWarning('Variable assignment found within a condition. Did you mean to do a comparison?', $hasAssignment, $errorCode);
            } else {
                $this->phpcsFile->addWarning('Assignment found within a condition. Did you mean to do a comparison?', $hasAssignment, 'NonVariableAssignmentFound');
            }
            $startPos = $hasAssignment;
        } while ($startPos < $closer);
    }
}
