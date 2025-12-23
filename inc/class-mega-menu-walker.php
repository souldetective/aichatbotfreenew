<?php
/**
 * Custom walker for mega menu rendering.
 *
 * @package aichatbotfree
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walker class to output mega menu markup using WordPress menus.
 */
class Aichatbotfree_Mega_Menu_Walker extends Walker_Nav_Menu {
	/**
	 * Track when a given depth is inside a mega menu.
	 *
	 * @var array
	 */
	protected $mega_menu_flags = [];

	/**
	 * Track the active mega menu column class.
	 *
	 * @var string
	 */
	protected $current_mega_columns = 'mega-col-3';

	/**
	 * Label used for aria on mega panels.
	 *
	 * @var string
	 */
	protected $current_mega_label = '';

	/**
	 * Optional limit class for links within a column.
	 *
	 * @var string
	 */
	protected $current_column_limit = '';

	/**
	 * Determine whether the menu item activates a mega menu.
	 *
	 * @param WP_Post $item Menu item.
	 *
	 * @return bool
	 */
	protected function is_mega_menu_item( $item ) {
		return in_array( 'mega-menu', (array) $item->classes, true );
	}

	/**
	 * Extract the grid column class from menu item classes.
	 *
	 * @param array $classes Menu item classes.
	 *
	 * @return string
	 */
	protected function get_columns_class( $classes ) {
		foreach ( $classes as $class ) {
			if ( preg_match( '/mega-col-(\d+)/', $class, $matches ) ) {
				return 'mega-col-' . absint( $matches[1] );
			}
		}

		return 'mega-col-3';
	}

	/**
	 * Extract the link limit utility class from menu item classes.
	 *
	 * @param array $classes Menu item classes.
	 *
	 * @return string
	 */
	protected function get_limit_class( $classes ) {
		foreach ( $classes as $class ) {
			if ( preg_match( '/limit-(\d+)/', $class, $matches ) ) {
				return 'limit-' . absint( $matches[1] );
			}
		}

		return '';
	}

	/**
	 * Starts the list before the elements are added.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$args            = (object) $args;
		$indent          = str_repeat( "\t", $depth );
		$is_mega_context = ! empty( $this->mega_menu_flags[ $depth ] );

		if ( 'discard' === ( $args->item_spacing ?? '' ) ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$classes   = [ 'sub-menu' ];
		$role_attr = '';

		if ( 0 === $depth && $is_mega_context ) {
			$classes[] = 'mega-menu__columns';
			$classes[] = $this->current_mega_columns;

			if ( $this->current_mega_label ) {
				$role_attr = sprintf( ' aria-label="%s"', esc_attr( $this->current_mega_label ) );
			}

			$output .= "{$n}{$indent}<div class=\"mega-menu__panel\" role=\"region\"{$role_attr}>{$n}";
			$output .= "{$indent}{$t}<div class=\"mega-menu__grid\">{$n}";
			$output .= "{$indent}{$t}{$t}<ul class=\"" . esc_attr( implode( ' ', $classes ) ) . "\">{$n}";

			return;
		}

		if ( 1 === $depth && $is_mega_context ) {
			$classes[] = 'mega-menu__links';

			if ( $this->current_column_limit ) {
				$classes[] = $this->current_column_limit;
			}
		}

		$class_names = implode( ' ', array_map( 'esc_attr', $classes ) );

		$output .= "{$n}{$indent}<ul class=\"{$class_names}\">{$n}";
	}

	/**
	 * Ends the list of after the elements are added.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$args            = (object) $args;
		$indent          = str_repeat( "\t", $depth );
		$is_mega_context = ! empty( $this->mega_menu_flags[ $depth ] );

		if ( 'discard' === ( $args->item_spacing ?? '' ) ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		if ( 0 === $depth && $is_mega_context ) {
			$output .= "{$indent}{$t}{$t}</ul>{$n}";
			$output .= "{$indent}{$t}</div>{$n}";
			$output .= "{$indent}</div>{$n}";

			return;
		}

		$output .= "{$indent}</ul>{$n}";
	}

	/**
	 * Starts the element output.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$args = (object) $args;

		if ( 0 === $depth ) {
			$this->mega_menu_flags[ $depth ] = $this->is_mega_menu_item( $item );

			if ( $this->mega_menu_flags[ $depth ] ) {
				$this->current_mega_columns = $this->get_columns_class( (array) $item->classes );
				$this->current_mega_label   = wp_strip_all_tags( $item->title );
			}
		} else {
			$this->mega_menu_flags[ $depth ] = ! empty( $this->mega_menu_flags[ $depth - 1 ] );
		}

		if ( 1 === $depth && ! empty( $this->mega_menu_flags[ $depth ] ) ) {
			$this->current_column_limit = $this->get_limit_class( (array) $item->classes );
		}

		if ( 'discard' === ( $args->item_spacing ?? '' ) ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ( ! empty( $this->mega_menu_flags[ $depth ] ) ) {
			$classes[] = 'is-mega-level';

			if ( 0 === $depth ) {
				$classes[] = 'has-mega-menu';
			}
		}

		/**
		 * Filters the list of CSS classes applied to a menu item's list item element.
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu item's <li> element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's <li> element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = [];
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
		$item_output .= '</a>';
		$item_output .= $args->after ?? '';

		/**
		 * Filters a menu item's starting output.
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";

		if ( 1 === $depth ) {
			$this->current_column_limit = '';
		}

		if ( 0 === $depth ) {
			$this->current_mega_columns = 'mega-col-3';
			$this->current_mega_label   = '';
		}
	}
}
