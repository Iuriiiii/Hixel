<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PageCompressor
{
    private const ENABLED = true;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $buffer = $response->getContent();

        if(!Self::ENABLED)
            return $response;

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/', // Remove HTML comments
            '/ {2,}/',
            '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s',
            '/>\s+</'
        );
    
        $replace = array(
            '>',
            '<',
            '\\1',
            '',
            ' ',
            '',
            '><'
        );

        $buffer = preg_replace($search,$replace,$buffer);

        //remove optional ending tags (see http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission )
        $remove = [
            '</option>','</li>','</dt>','</dd>','</tr>','</th>','</td>'
        ];

        $buffer = str_ireplace($remove,'',$buffer);

        $response->setContent($buffer);
        return $response;
    }
}
