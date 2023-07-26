<?php

namespace App\Model;

interface Product
{
    public function getArticleNumber(): ArticleNumber;

    public function getName(): string;

    public function getBrand(): Brand;
}
