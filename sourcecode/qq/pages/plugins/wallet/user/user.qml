<!-- 钱包信息 -->
<view class="wallet bg-white">
  <view class="item normal oh">
    <view class="fl cr-666 name">有效</view>
    <view class="fl cr-main money single-text">{{user_wallet.normal_money || '0.00'}}</view>
    <view class="fl cr-888 unit">元</view>
  </view>
  <view class="item frozen oh">
    <view class="fl cr-666 name">冻结</view>
    <view class="fl money single-text">{{user_wallet.frozen_money || '0.00'}}</view>
    <view class="fl cr-888 unit">元</view>
  </view>
  <view class="item give oh">
    <view class="fl cr-666 name">赠送</view>
    <view class="fl money single-text">{{user_wallet.give_money || '0.00'}}</view>
    <view class="fl cr-888 unit">元</view>
  </view>
  <view class="submit">
    <navigator qq:if="{{(data_base || null) == null || data_base.is_enable_recharge == 1}}" url="/pages/plugins/wallet/recharge/recharge" hover-class="none" class="fl">
      <button size="mini" type="default" hover-class="none" class="submit-recharge">充值</button>
    </navigator>
    <navigator qq:if="{{(data_base || null) == null || data_base.is_enable_cash == 1}}"  url="/pages/plugins/wallet/cash-auth/cash-auth" hover-class="none" class="fl">
      <button size="mini" type="default" hover-class="none" class="submit-cash">提现</button>
    </navigator>
  </view>
</view>

<!-- 导航 -->
<view qq:if="{{nav_list.length > 0}}" class="nav spacing-mt oh bg-white">
  <block qq:for="{{nav_list}}" qq:key="key">
    <navigator url="{{item.url}}" hover-class="none">
      <view class="item fl tc">
        <image src="{{item.icon}}" mode="scaleToFill" class="dis-block" />
        <view class="title">{{item.title}}</view>
      </view>
    </navigator>
  </block>
</view>

<!-- 通知  -->
<view qq:if="{{(data_base.user_center_notice || null) != null && data_base.user_center_notice.length > 0}}" class="tips-container spacing-mt spacing-mb">
  <view class="tips">
    <view qq:for="{{data_base.user_center_notice}}" qq:key="key" class="item">
      {{item}}
    </view>
  </view>
</view>