<?php
/**
 * Copyright (C) 2014, 2015 Dmytro Dzyubak
 * 
 * This file is part of php-pim.
 * 
 * php-pim is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php-pim is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php-pim. If not, see <http://www.gnu.org/licenses/>.
 */

class Paginator {
    public static function paginatedLink($action, $inactive, $text, $offset = '') {
        if ($inactive) {
            return '<span class="inactive">'.$text.'</span>';
        } else {
            return '<span class="active">'
                .'<a href="'.BASE_URL.$action.'/'.$offset.'">'.$text.'</a>'
                .'</span>';
            //
        }
    }
    
    public static function paginatedLinks($action, $total, $offset, $perPage) {
        $separator = ' | ';
        // "<<Prev" link
        $out = self::paginatedLink($action, $offset == 1, '<< Prev', $offset - $perPage);
        // all groupings except last one
        for( $start = 1, $end = $perPage; $end < $total; $start += $perPage, $end += $perPage) {
            $out .= $separator;
            $out .= self::paginatedLink($action, $offset == $start, $start."-".$end, $start);
        }
        // at this point, $start points to the element at the beginning of the last grouping
        $end = ($total > $start) ? "-".$total : ""; // last grouping should be "11", not "11-11"
        $out .= $separator;
        $out .= self::paginatedLink($action, $offset == $start, $start.$end, $start);
        // print "Next>>" link
        $out .= $separator;
        $out .= self::paginatedLink($action, $offset == $start, 'Next >>', $offset + $perPage);
        return $out;
    }
}
