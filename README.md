# docker-ip-filter
对docker开放的端口， 限制为只允许某个ip访问

如何限制docker暴露的对外访问端口

 
docker 会在iptables上加上自己的转发规则，如果直接在input链上限制端口是没有效果的。这就需要限制docker的转发链上的DOCKER表。

##### 查询DOCKER表并显示规则编号
```iptables -L DOCKER -n --line-number```
##### 修改对应编号的iptables 规则，这里添加了允许访问ip的限制
```iptables -R DOCKER 5 -p tcp -m tcp -s 192.168.1.0/24 --dport 3000 -j ACCEPT```
