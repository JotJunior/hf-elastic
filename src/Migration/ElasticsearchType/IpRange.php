<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

class IpRange extends Range
{

    public Type $type = Type::ipRange;

}