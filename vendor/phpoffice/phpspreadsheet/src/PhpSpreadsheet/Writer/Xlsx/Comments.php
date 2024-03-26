<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;

class Comments extends WriterPart
{
    
    public function writeComments(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $comments = $pWorksheet->getComments();

        
        $authors = [];
        $authorId = 0;
        foreach ($comments as $comment) {
            if (!isset($authors[$comment->getAuthor()])) {
                $authors[$comment->getAuthor()] = $authorId++;
            }
        }

        
        $objWriter->startElement('comments');
        $objWriter->writeAttribute('xmlns', 'http:

        
        $objWriter->startElement('authors');
        foreach ($authors as $author => $index) {
            $objWriter->writeElement('author', $author);
        }
        $objWriter->endElement();

        
        $objWriter->startElement('commentList');
        foreach ($comments as $key => $value) {
            $this->writeComment($objWriter, $key, $value, $authors);
        }
        $objWriter->endElement();

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeComment(XMLWriter $objWriter, $pCellReference, Comment $pComment, array $pAuthors): void
    {
        
        $objWriter->startElement('comment');
        $objWriter->writeAttribute('ref', $pCellReference);
        $objWriter->writeAttribute('authorId', $pAuthors[$pComment->getAuthor()]);

        
        $objWriter->startElement('text');
        $this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $pComment->getText());
        $objWriter->endElement();

        $objWriter->endElement();
    }

    
    public function writeVMLComments(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pWorksheet)
    {
        
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        
        $comments = $pWorksheet->getComments();

        
        $objWriter->startElement('xml');
        $objWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
        $objWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
        $objWriter->writeAttribute('xmlns:x', 'urn:schemas-microsoft-com:office:excel');

        
        $objWriter->startElement('o:shapelayout');
        $objWriter->writeAttribute('v:ext', 'edit');

        
        $objWriter->startElement('o:idmap');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('data', '1');
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('v:shapetype');
        $objWriter->writeAttribute('id', '_x0000_t202');
        $objWriter->writeAttribute('coordsize', '21600,21600');
        $objWriter->writeAttribute('o:spt', '202');
        $objWriter->writeAttribute('path', 'm,l,21600r21600,l21600,xe');

        
        $objWriter->startElement('v:stroke');
        $objWriter->writeAttribute('joinstyle', 'miter');
        $objWriter->endElement();

        
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('gradientshapeok', 't');
        $objWriter->writeAttribute('o:connecttype', 'rect');
        $objWriter->endElement();

        $objWriter->endElement();

        
        foreach ($comments as $key => $value) {
            $this->writeVMLComment($objWriter, $key, $value);
        }

        $objWriter->endElement();

        
        return $objWriter->getData();
    }

    
    private function writeVMLComment(XMLWriter $objWriter, $pCellReference, Comment $pComment): void
    {
        
        [$column, $row] = Coordinate::coordinateFromString($pCellReference);
        $column = Coordinate::columnIndexFromString($column);
        $id = 1024 + $column + $row;
        $id = substr($id, 0, 4);

        
        $objWriter->startElement('v:shape');
        $objWriter->writeAttribute('id', '_x0000_s' . $id);
        $objWriter->writeAttribute('type', '#_x0000_t202');
        $objWriter->writeAttribute('style', 'position:absolute;margin-left:' . $pComment->getMarginLeft() . ';margin-top:' . $pComment->getMarginTop() . ';width:' . $pComment->getWidth() . ';height:' . $pComment->getHeight() . ';z-index:1;visibility:' . ($pComment->getVisible() ? 'visible' : 'hidden'));
        $objWriter->writeAttribute('fillcolor', '#' . $pComment->getFillColor()->getRGB());
        $objWriter->writeAttribute('o:insetmode', 'auto');

        
        $objWriter->startElement('v:fill');
        $objWriter->writeAttribute('color2', '#' . $pComment->getFillColor()->getRGB());
        $objWriter->endElement();

        
        $objWriter->startElement('v:shadow');
        $objWriter->writeAttribute('on', 't');
        $objWriter->writeAttribute('color', 'black');
        $objWriter->writeAttribute('obscured', 't');
        $objWriter->endElement();

        
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('o:connecttype', 'none');
        $objWriter->endElement();

        
        $objWriter->startElement('v:textbox');
        $objWriter->writeAttribute('style', 'mso-direction-alt:auto');

        
        $objWriter->startElement('div');
        $objWriter->writeAttribute('style', 'text-align:left');
        $objWriter->endElement();

        $objWriter->endElement();

        
        $objWriter->startElement('x:ClientData');
        $objWriter->writeAttribute('ObjectType', 'Note');

        
        $objWriter->writeElement('x:MoveWithCells', '');

        
        $objWriter->writeElement('x:SizeWithCells', '');

        
        $objWriter->writeElement('x:AutoFill', 'False');

        
        $objWriter->writeElement('x:Row', ($row - 1));

        
        $objWriter->writeElement('x:Column', ($column - 1));

        $objWriter->endElement();

        $objWriter->endElement();
    }
}
