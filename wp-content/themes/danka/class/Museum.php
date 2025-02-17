<?php

class Museum extends \Timber\Post {
    
    public $days = array(
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    );
    
    public function get_current_hours() {
        $current_hours = null;
        $now = new DateTime( 'now', new DateTimeZone( 'Europe/Paris' ) );
        $current_day = $this->days[$now->format('N') - 1];
        $hours = $this->get_field('hours');
        $special_hours = $this->get_field('special_hours');

        // Si le musée est fermé aujourd'hui
        if ( $hours['closing_days'] && in_array( $current_day, $hours['closing_days'] ) ) {
            return null;
        }

        // Si le musée a des horaires spécifiques aujourd'hui
        else if ( $hours[$current_day . '_items'] && in_array( $current_day, $hours['specific_hours'] ) ) {
            return $hours[$current_day . '_items'];
        }
        else {
            $current_hours = $hours['items'];
        }

        // S'il existe des horaires pour des dates spécifiques
        if ( $special_hours ) {
            foreach ( $special_hours as $special_hour ) {
                $date = new DateTime( $special_hour['date'], new DateTimeZone( 'Europe/Paris' ) );
                $has_special_hours = false;

                if ( $special_hour['every_year'] ) {
                    if ( $now->format( 'md' ) == $date->format( 'md' ) ) {
                        $has_special_hours = true;
                    }
                    else if ( $now->format( 'Ymd' ) == $date->format( 'Ymd' ) ) {
                        $has_special_hours = true;
                    }
                }

                if ( !$has_special_hours ) {
                    continue;
                }
                else if ( $special_hour['is_closed'] ) {
                    return null;
                }
                else if ( $special_hour['hours'] ) {
                    return $special_hour['hours'];
                }
            }
        }

        return $current_hours;
    }

	public function is_open() {
        $is_open = false;
        $hours = $this->get_current_hours();
        
        if ( $hours ) {
            $last_closing = null;
            $now = new DateTime( 'now', new DateTimeZone( 'Europe/Paris' ) );

            foreach ( $hours as $hour ) {
                $explode_end = explode( ':', $hour['closing'] );

                if ( $hour['opening'] && $hour['closing'] ) {
                    $end = new DateTime( 'now', new DateTimeZone( 'Europe/Paris' ) );
                    $end->setTime( $explode_end[0], $explode_end[1], 0 );
        
                    if ( $last_closing === null || $end > $last_closing ) {
                        $last_closing = $end;
                    }
                }
            }
    
            if ( $last_closing >= $now ) {
                $is_open = true;
            }
        }

        return $is_open;
    }
}
