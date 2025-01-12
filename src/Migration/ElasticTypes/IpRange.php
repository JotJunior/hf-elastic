<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

class IpRange extends Range
{

    public Type $type = Type::ipRange;

}