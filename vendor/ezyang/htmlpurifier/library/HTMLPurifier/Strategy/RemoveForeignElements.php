<?php



class HTMLPurifier_Strategy_RemoveForeignElements extends HTMLPurifier_Strategy
{

    
    public function execute($tokens, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $generator = new HTMLPurifier_Generator($config, $context);
        $result = array();

        $escape_invalid_tags = $config->get('Core.EscapeInvalidTags');
        $remove_invalid_img = $config->get('Core.RemoveInvalidImg');

        
        $trusted = $config->get('HTML.Trusted');
        $comment_lookup = $config->get('HTML.AllowedComments');
        $comment_regexp = $config->get('HTML.AllowedCommentsRegexp');
        $check_comments = $comment_lookup !== array() || $comment_regexp !== null;

        $remove_script_contents = $config->get('Core.RemoveScriptContents');
        $hidden_elements = $config->get('Core.HiddenElements');

        
        if ($remove_script_contents === true) {
            $hidden_elements['script'] = true;
        } elseif ($remove_script_contents === false && isset($hidden_elements['script'])) {
            unset($hidden_elements['script']);
        }

        $attr_validator = new HTMLPurifier_AttrValidator();

        
        $remove_until = false;

        
        $textify_comments = false;

        $token = false;
        $context->register('CurrentToken', $token);

        $e = false;
        if ($config->get('Core.CollectErrors')) {
            $e =& $context->get('ErrorCollector');
        }

        foreach ($tokens as $token) {
            if ($remove_until) {
                if (empty($token->is_tag) || $token->name !== $remove_until) {
                    continue;
                }
            }
            if (!empty($token->is_tag)) {
                

                
                if (isset($definition->info_tag_transform[$token->name])) {
                    $original_name = $token->name;
                    
                    
                    $token = $definition->
                        info_tag_transform[$token->name]->transform($token, $config, $context);
                    if ($e) {
                        $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Tag transform', $original_name);
                    }
                }

                if (isset($definition->info[$token->name])) {
                    
                    
                    if (($token instanceof HTMLPurifier_Token_Start || $token instanceof HTMLPurifier_Token_Empty) &&
                        $definition->info[$token->name]->required_attr &&
                        ($token->name != 'img' || $remove_invalid_img) 
                    ) {
                        $attr_validator->validateToken($token, $config, $context);
                        $ok = true;
                        foreach ($definition->info[$token->name]->required_attr as $name) {
                            if (!isset($token->attr[$name])) {
                                $ok = false;
                                break;
                            }
                        }
                        if (!$ok) {
                            if ($e) {
                                $e->send(
                                    E_ERROR,
                                    'Strategy_RemoveForeignElements: Missing required attribute',
                                    $name
                                );
                            }
                            continue;
                        }
                        $token->armor['ValidateAttributes'] = true;
                    }

                    if (isset($hidden_elements[$token->name]) && $token instanceof HTMLPurifier_Token_Start) {
                        $textify_comments = $token->name;
                    } elseif ($token->name === $textify_comments && $token instanceof HTMLPurifier_Token_End) {
                        $textify_comments = false;
                    }

                } elseif ($escape_invalid_tags) {
                    
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_RemoveForeignElements: Foreign element to text');
                    }
                    $token = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token)
                    );
                } else {
                    
                    
                    if (isset($hidden_elements[$token->name])) {
                        if ($token instanceof HTMLPurifier_Token_Start) {
                            $remove_until = $token->name;
                        } elseif ($token instanceof HTMLPurifier_Token_Empty) {
                            
                        } else {
                            $remove_until = false;
                        }
                        if ($e) {
                            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign meta element removed');
                        }
                    } else {
                        if ($e) {
                            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign element removed');
                        }
                    }
                    continue;
                }
            } elseif ($token instanceof HTMLPurifier_Token_Comment) {
                
                if ($textify_comments !== false) {
                    $data = $token->data;
                    $token = new HTMLPurifier_Token_Text($data);
                } elseif ($trusted || $check_comments) {
                    
                    $trailing_hyphen = false;
                    if ($e) {
                        
                        if (substr($token->data, -1) == '-') {
                            $trailing_hyphen = true;
                        }
                    }
                    $token->data = rtrim($token->data, '-');
                    $found_double_hyphen = false;
                    while (strpos($token->data, '--') !== false) {
                        $found_double_hyphen = true;
                        $token->data = str_replace('--', '-', $token->data);
                    }
                    if ($trusted || !empty($comment_lookup[trim($token->data)]) ||
                        ($comment_regexp !== null && preg_match($comment_regexp, trim($token->data)))) {
                        
                        if ($e) {
                            if ($trailing_hyphen) {
                                $e->send(
                                    E_NOTICE,
                                    'Strategy_RemoveForeignElements: Trailing hyphen in comment removed'
                                );
                            }
                            if ($found_double_hyphen) {
                                $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Hyphens in comment collapsed');
                            }
                        }
                    } else {
                        if ($e) {
                            $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
                        }
                        continue;
                    }
                } else {
                    
                    if ($e) {
                        $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
                    }
                    continue;
                }
            } elseif ($token instanceof HTMLPurifier_Token_Text) {
            } else {
                continue;
            }
            $result[] = $token;
        }
        if ($remove_until && $e) {
            
            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Token removed to end', $remove_until);
        }
        $context->destroy('CurrentToken');
        return $result;
    }
}


