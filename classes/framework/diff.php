<?php
/**
 * arbit diff algorithm
 *
 * This file is part of arbit.
 *
 * arbit is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * arbit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with arbit; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * This is a implemntation of the common diff algorithm for arbritrary arrays
 * of tokens. This way you may diff on line / word / character / ... basis.
 *
 * Extensions of this class may offer ways to split your text into the desired
 * token type and apply and return the diff for them. The diffs are not
 * rendered by this class.
 *
 * This class implements the trivial intuitive diff algorithm. You may change
 * the implementation by an optimized variant of the algorithm.;
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitFrameworkDiff
{
    /**
     * Diff two arrays
     *
     * Return the diff of two arrays, as an array consisting of arbitDiffToken
     * elements with all have a status of the token (old, added, removed), the
     * token number and the actual content.
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    public function diff( array $old, array $new )
    {
        // Reduce same tokens at the beginning, without starting the actual
        // LCS algorithm.
        $start = array();
        $oldLength = count( $old );
        $newLength = count( $new );
        $length = min( $oldLength, $newLength );
        for ( $i = 0; $i < $length; ++$i )
        {
            if ( $old[$i] === $new[$i] )
            {
                $start[] = $old[$i];
                unset( $old[$i], $new[$i] );
            }
            else
            {
                break;
            }
        }

        // Also reduce same tokens at the end, without starting the actual LCS
        // algorithm.
        $end = array();
        $length -= $i;
        for ( $i = 1; $i < $length; ++$i )
        {
            if ( $old[$oldLength - $i] === $new[$newLength - $i] )
            {
                array_unshift( $end, $old[$oldLength - $i] );
                unset( $old[$oldLength - $i], $new[$newLength - $i] );
            }
            else
            {
                break;
            }
        }

        // Calculate the LCS for remaining tokens.
        $common = $this->longestCommonSubsequence( array_values( $old ), array_values( $new ) );

        // Build diff
        $diff = array();
        $line = 0;
        foreach ( $start as $token )
        {
            $diff[] = new arbitFrameworkDiffToken( $token, arbitFrameworkDiffToken::OLD );
        }

        reset( $old );
        reset( $new );
        foreach ( $common as $token )
        {
            // Add removed tokens to diff
            while ( ( ( $oldToken = reset( $old ) ) !== $token ) )
            {
                $diff[] = new arbitFrameworkDiffToken( array_shift( $old ), arbitFrameworkDiffToken::REMOVED );
            }

            // Add new tokens to diff
            while ( ( ( $newToken = reset( $new ) ) !== $token ) )
            {
                $diff[] = new arbitFrameworkDiffToken( array_shift( $new ), arbitFrameworkDiffToken::ADDED );
            }

            // Add unchanged token from LCS to diff.
            $diff[] = new arbitFrameworkDiffToken( $token, arbitFrameworkDiffToken::OLD );
            array_shift( $old );
            array_shift( $new );
        }

        // Add all tokens still in the old and new lists to diff
        while ( ( $token = array_shift( $old ) ) !== null )
        {
            $diff[] = new arbitFrameworkDiffToken( $token, arbitFrameworkDiffToken::REMOVED );
        }

        while ( ( $token = array_shift( $new ) ) !== null )
        {
            $diff[] = new arbitFrameworkDiffToken( $token, arbitFrameworkDiffToken::ADDED );
        }

        // Append prior removed stuff
        foreach ( $end as $token )
        {
            $diff[] = new arbitFrameworkDiffToken( $token, arbitFrameworkDiffToken::OLD );
        }

        return $diff;
    }

    /**
     * Calculate LCS of two token lists
     *
     * Calculate and return the longest common subsequence of two token arrays.
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    public function longestCommonSubsequence( array $old, array $new )
    {
        $matrix = array();

        $oldLength = count( $old );
        $newLength = count( $new );

        for ( $i = 0; $i <= $oldLength; ++$i )
        {
            $matrix[$i][0] = 0;
        }

        for ( $j = 0; $j <= $newLength; ++$j )
        {
            $matrix[0][$j] = 0;
        }

        // Calculate longest common subsequence
        $longest = 0;
        for ( $i = 1; $i <= $oldLength; ++$i )
        {
            for ( $j = 1; $j <= $newLength; ++$j )
            {
                $matrix[$i][$j] = max(
                    $matrix[$i - 1][$j],
                    $matrix[$i][$j - 1],
                    ( ( $old[$i - 1] === $new[$j - 1] ) ? $matrix[$i - 1][$j - 1] + 1 : 0 )
                );
            }
        }

        // Backtrace to read common longest subsequence
        return $this->backtraceLCS( $matrix, $old, $new, $oldLength, $newLength );
    }

    /**
     * Backtrace LCS
     *
     * LCS calculated the matrix for the determination of the LCS. This
     * function calculates the actual token list back from this matrix by
     * backtracing the best path in the matrix.
     *
     * @param array $matrix
     * @param array $old
     * @param array $new
     * @param int $i
     * @param int $j
     * @return array
     */
    protected function backtraceLCS( array $matrix, array $old, array $new, $i, $j )
    {
        $common = array();
        while ( ( $i > 0 ) &&
                ( $j > 0 ) )
        {
            if ( $old[$i - 1] === $new[$j - 1] )
            {
                array_unshift( $common, $old[$i - 1] );
                --$i;
                --$j;
            }
            elseif ( $matrix[$i][$j - 1] > $matrix[$i - 1][$j] )
            {
                --$j;
            }
            else
            {
                --$i;
            }
        }

        return $common;
    }
}

