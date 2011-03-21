<?php
/**
 * Template for a Facebook story entry. Expects the following to be set:
 *
 * $this->actorImgUrl        URI to the actor's pic.
 * $this->actorProfileLink   Full link <a ...>Text</a> to FB Profile
 * $this->message            The body of the post
 * $this->attachement        Attachement array
 * $this->postId             The postId
 * $this->postInfo           Text to display for post info (post time etc...)
 * $this->likesInfo          Text to display for the Like info (You and one other person etc...)
 */
?>
<div class="hordeSmStreamstory">
 <div class="hordeSmAvatar"><img width="48px" height="48px" src="<?php echo $this->actorImgUrl ?>" alt="<?php echo $this->actorName ?>" /></div>
 <div class="hordeSmStreambody">
  <?php echo $this->actorProfileLink . $this->actorName?></a><br />
  <?php echo empty($this->message) ? '' : $this->message;?>
  <?php if(!empty($this->attachment) &&
           (!empty($this->attachment['description']) ||
            !empty($this->attachment['media']) ||
            !empty($this->attachment['caption']))):?>
    <div class="fbattachment solidbox">
      <?php if (!empty($this->attachment['media']) && count($this->attachment['media'])):?>
        <div class="fbmedia<?php echo count($this->attachment['media']) > 1 ? ' fbmediawide' : ''?>">
          <?php foreach($this->attachment['media'] as $item): ?>
            <div class="fbmediaitem<?php echo (count($this->attachement['media']) > 1) ? ' fbmediaitemmultiple' : ' fbmediaitemsingle'?>">
              <?php echo Horde::externalUrl($item['href'], true) ?><img alt="[image]"src="<?php echo htmlspecialchars($item['src'])?>" /></a>
            </div>
          <?php endforeach;?>
        </div>
      <?php endif;?>
      <?php if (!empty($this->attachment['name'])):?>
        <div class="fbattachmenttitle">
          <?php echo Horde::externalUrl($this->attachment['href'], true) . $this->attachment['name']?></a>
        </div>
      <?php endif;?>
      <?php if (!empty($this->attachment['caption'])):?>
        <div class="fbattachmentcaption"><?php echo $this->attachment['caption']?></div>
      <?php endif;?>
      <?php if (!empty($this->attachment['description'])):?>
        <div class="fbattachmentcopy"><?php echo $this->attachment['description']?></div>
      <?php endif;?>
    </div>
  <?php endif;?>
  <div class="hordeSmStreaminfo"><?php echo $this->postInfo?></div>
  <div class="hordeSmStreaminfo" id="fb<?php echo $this->postId?>"><?php echo $this->likesInfo?></div>
 </div>
</div>
<div class="fbcontentdivider">&nbsp;</div>