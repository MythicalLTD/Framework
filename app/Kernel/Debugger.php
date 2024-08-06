<?php

namespace MythicalSystemsFramework\Kernel;

class Debugger
{
    /**
     * Display the information.
     *
     * @param mixed $input The input to display the info about!
     * @param bool $collapse This is always false by default
     */
    public static function display_info($input, $collapse = false): void
    {
        try {
            $recursive = function ($data, $level = 0) use (&$recursive, $collapse) {
                global $argv;

                $isTerminal = isset($argv);

                if (!$isTerminal && $level == 0 && !defined('DUMP_DEBUG_SCRIPT')) {
                    define('DUMP_DEBUG_SCRIPT', true);

                    echo '<script language="Javascript">function toggleDisplay(id) {';
                    echo 'var state = document.getElementById("container"+id).style.display;';
                    echo 'document.getElementById("container"+id).style.display = state == "inline" ? "none" : "inline";';
                    echo 'document.getElementById("plus"+id).style.display = state == "inline" ? "inline" : "none";';
                    echo '}</script>' . "\n";
                }

                $type = !is_string($data) && is_callable($data) ? 'Callable' : ucfirst(gettype($data));
                $type_data = null;
                $type_color = null;
                $type_length = null;

                switch ($type) {
                    case 'String':
                        $type_color = 'green';
                        $type_length = strlen($data);
                        $type_data = '"' . htmlentities($data) . '"';
                        break;

                    case 'Double':
                    case 'Float':
                        $type = 'Float';
                        $type_color = '#0099c5';
                        $type_length = strlen($data);
                        $type_data = htmlentities($data);
                        break;

                    case 'Integer':
                        $type_color = 'red';
                        $type_length = strlen($data);
                        $type_data = htmlentities($data);
                        break;

                    case 'Boolean':
                        $type_color = '#92008d';
                        $type_length = strlen($data);
                        $type_data = $data ? 'TRUE' : 'FALSE';
                        break;

                    case 'NULL':
                        $type_length = 0;
                        break;

                    case 'Array':
                        $type_length = count($data);
                }

                if (in_array($type, ['Object', 'Array'])) {
                    $notEmpty = false;

                    foreach ($data as $key => $value) {
                        if (!$notEmpty) {
                            $notEmpty = true;

                            if ($isTerminal) {
                                echo $type . ($type_length !== null ? '(' . $type_length . ')' : '') . "\n";
                            } else {
                                $id = substr(md5(mt_rand() . ':' . $key . ':' . $level), 0, 8);

                                echo "<a href=\"javascript:toggleDisplay('" . $id . "');\" style=\"text-decoration:none\">";
                                echo "<span style='color:#666666'>" . $type . ($type_length !== null ? '(' . $type_length . ')' : '') . '</span>';
                                echo '</a>';
                                echo '<span id="plus' . $id . '" style="display: ' . ($collapse ? 'inline' : 'none') . ';">&nbsp;&#10549;</span>';
                                echo '<div id="container' . $id . '" style="display: ' . ($collapse ? '' : 'inline') . ';">';
                                echo '<br />';
                            }

                            for ($i = 0; $i <= $level; ++$i) {
                                echo $isTerminal ? '|    ' : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            }

                            echo $isTerminal ? "\n" : '<br />';
                        }

                        for ($i = 0; $i <= $level; ++$i) {
                            echo $isTerminal ? '|    ' : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }

                        echo $isTerminal ? '[' . $key . '] => ' : "<span style='color:black'>[" . $key . ']&nbsp;=>&nbsp;</span>';

                        call_user_func($recursive, $value, $level + 1);
                    }

                    if ($notEmpty) {
                        for ($i = 0; $i <= $level; ++$i) {
                            echo $isTerminal ? '|    ' : "<span style='color:black'>|</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }

                        if (!$isTerminal) {
                            echo '</div>';
                        }
                    } else {
                        echo $isTerminal ?
                            $type . ($type_length !== null ? '(' . $type_length . ')' : '') . '  ' :
                            "<span style='color:#666666'>" . $type . ($type_length !== null ? '(' . $type_length . ')' : '') . '</span>&nbsp;&nbsp;';
                    }
                } else {
                    echo $isTerminal ?
                        $type . ($type_length !== null ? '(' . $type_length . ')' : '') . '  ' :
                        "<span style='color:#666666'>" . $type . ($type_length !== null ? '(' . $type_length . ')' : '') . '</span>&nbsp;&nbsp;';

                    if ($type_data != null) {
                        echo $isTerminal ? $type_data : "<span style='color:" . $type_color . "'>" . $type_data . '</span>';
                    }
                }

                echo $isTerminal ? "\n" : '<br />';
            };

            call_user_func($recursive, $input);
            exit;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Throw an error.
     *
     * @param mixed $renderer The twig renderer
     * @param string $error_text The error text
     * @param string $error_full_message The full error message
     * @param int $error_database_id The error database id
     * @param string $error_file_path The error file path
     *
     * @return void IT will die
     */
    public static function throw_error($renderer, $error_text, $error_full_message, $error_database_id, $error_file_path)
    {
        try {
            $router = new \Router\Router();
            $renderer->addGlobal('error_message', $error_text);
            $renderer->addGlobal('error_full_message', $error_full_message);
            $renderer->addGlobal('error_id', $error_database_id);
            $renderer->addGlobal('error_path', $error_file_path);

            $router->add('/(.*)', function () {
                global $renderer;

                http_response_code(500);
                exit($renderer->render('/errors/debug.twig'));
            });

            exit($router->route());
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }
}
