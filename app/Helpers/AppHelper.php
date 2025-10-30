<?php
/**
 * Get the current tenant (store)
 * 
 */
function tenant() {
    return app('store');
}