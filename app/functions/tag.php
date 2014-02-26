<?php

/**
 * 使用 function 来调用 Phalcon\Tag，以便于更容易在 view 中编码
 *
 * @see http://docs.phalconphp.com/en/latest/reference/tags.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Tag.html
 */

function get_auto_escape()
{
    return Phalcon\Tag::getAutoEscape();
}

function set_auto_escape($autoescape)
{
    return Phalcon\Tag::setAutoEscape($autoescape);
}

function set_default($id, $value)
{
    return Phalcon\Tag::setDefault($id, $value);
}

function set_defaults($values)
{
    return Phalcon\Tag::setDefaults($values);
}

function has_value($name)
{
    return Phalcon\Tag::hasValue($name);
}

function get_value($name, $params = null)
{
    return Phalcon\Tag::getValue($name, $params);
}

function reset_input()
{
    return Phalcon\Tag::resetInput();
}

function link_to($parameters, $text = null)
{
    return Phalcon\Tag::linkTo($parameters, $text);
}

function text_field($parameters)
{
    return Phalcon\Tag::textField($parameters);
}

function number_field($parameters)
{
    return Phalcon\Tag::numberField($parameters);
}

function email_field($parameters)
{
    return Phalcon\Tag::emailField($parameters);
}

function date_field($parameters)
{
    return Phalcon\Tag::dateField($parameters);
}

function password_field($parameters)
{
    return Phalcon\Tag::passwordField($parameters);
}

function hidden_field($parameters)
{
    return Phalcon\Tag::hiddenField($parameters);
}

function file_field($parameters)
{
    return Phalcon\Tag::fileField($parameters);
}

function check_field($parameters)
{
    return Phalcon\Tag::checkField($parameters);
}

function radio_field($parameters)
{
    return Phalcon\Tag::radioField($parameters);
}

function image_input($parameters)
{
    return Phalcon\Tag::imageInput($parameters);
}

function submit_button($parameters)
{
    return Phalcon\Tag::submitButton($parameters);
}

function select_static($parameters, $data = array())
{
    return Phalcon\Tag::selectStatic($parameters, $data);
}

function select($parameters, $data = array())
{
    return Phalcon\Tag::select($parameters, $data);
}

function text_area($parameters)
{
    return Phalcon\Tag::textArea($parameters);
}

function form($parameters)
{
    return Phalcon\Tag::form($parameters);
}

function end_form()
{
    return Phalcon\Tag::endForm();
}

function set_title($title)
{
    return Phalcon\Tag::setTitle($title);
}

function append_title($title, $separator = ' - ')
{
    return Phalcon\Tag::appendTitle($title . $separator);
}

function prepend_title($title, $separator = ' - ')
{
    return Phalcon\Tag::prependTitle($title . $separator);
}

function get_title()
{
    return Phalcon\Tag::getTitle();
}

function stylesheet_link($parameters, $local = true)
{
    return Phalcon\Tag::stylesheetLink($tags);
}

function css($parameters, $local = true)
{
    return Phalcon\Tag::stylesheetLink($tags);
}

function javascript_include($parameters, $local = true)
{
    return Phalcon\Tag::javascriptInclude($tags);
}

function js($parameters, $local = true)
{
    return Phalcon\Tag::javascriptInclude($tags);
}

function image($parameters, $local = true)
{
    return Phalcon\Tag::image($tags);
}

function friendly_title($text, $separator = '-', $lowercase = true)
{
    return Phalcon\Tag::friendlyTitle($text, $separator, $lowercase);
}

function set_doc_type($docType)
{
    return Phalcon\Tag::setDocType($docType);
}

function get_doc_type()
{
    return Phalcon\Tag::getDocType();
}

function tag_html($tagName, $parameters = array(), $selfClose = false, $onlyStart = false, $useEol = false)
{
    return Phalcon\Tag::tagHtml($tagName, $parameters, $selfClose, $onlyStart, $useEol);
}

function tag_html_close($tagName, $useEol = false)
{
    return Phalcon\Tag::tag_html_close($tagName, $useEol);
}
