<?php

namespace GuzzleHttp\Psr7;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;


function str(MessageInterface $message)
{
    return Message::toString($message);
}


function uri_for($uri)
{
    return Utils::uriFor($uri);
}


function stream_for($resource = '', array $options = [])
{
    return Utils::streamFor($resource, $options);
}


function parse_header($header)
{
    return Header::parse($header);
}


function normalize_header($header)
{
    return Header::normalize($header);
}


function modify_request(RequestInterface $request, array $changes)
{
    return Utils::modifyRequest($request, $changes);
}


function rewind_body(MessageInterface $message)
{
    Message::rewindBody($message);
}


function try_fopen($filename, $mode)
{
    return Utils::tryFopen($filename, $mode);
}


function copy_to_string(StreamInterface $stream, $maxLen = -1)
{
    return Utils::copyToString($stream, $maxLen);
}


function copy_to_stream(StreamInterface $source, StreamInterface $dest, $maxLen = -1)
{
    return Utils::copyToStream($source, $dest, $maxLen);
}


function hash(StreamInterface $stream, $algo, $rawOutput = false)
{
    return Utils::hash($stream, $algo, $rawOutput);
}


function readline(StreamInterface $stream, $maxLength = null)
{
    return Utils::readLine($stream, $maxLength);
}


function parse_request($message)
{
    return Message::parseRequest($message);
}


function parse_response($message)
{
    return Message::parseResponse($message);
}


function parse_query($str, $urlEncoding = true)
{
    return Query::parse($str, $urlEncoding);
}


function build_query(array $params, $encoding = PHP_QUERY_RFC3986)
{
    return Query::build($params, $encoding);
}


function mimetype_from_filename($filename)
{
    return MimeType::fromFilename($filename);
}


function mimetype_from_extension($extension)
{
    return MimeType::fromExtension($extension);
}


function _parse_message($message)
{
    return Message::parseMessage($message);
}


function _parse_request_uri($path, array $headers)
{
    return Message::parseRequestUri($path, $headers);
}


function get_message_body_summary(MessageInterface $message, $truncateAt = 120)
{
    return Message::bodySummary($message, $truncateAt);
}


function _caseless_remove($keys, array $data)
{
    return Utils::caselessRemove($keys, $data);
}
