<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<span class="d-block">
		<xsl:text>#</xsl:text>
		<xsl:value-of select="event/id"/>
	</span>
	<h3>
		<xsl:if test="event/is_public=0">
			<xsl:value-of select="php:function('lang', 'Private event')"/>
		</xsl:if>
		<xsl:if test="event/is_public=1">
			<xsl:value-of select="event/name"/>
		</xsl:if>
	</h3>
	<span class="d-block">
		<xsl:value-of select="event/when"/>
	</span>
	<div>
		<span class="font-weight-bold text-uppercase">
			<xsl:value-of select="php:function('lang', 'Place')"/>:
		</span>
		<a href="{event/building_link}">
			<xsl:value-of select="event/building_name"/>
		</a>
		(<xsl:value-of select="event/resource_info"/>)
	</div>
	<div>
		<span class="font-weight-bold text-uppercase">
			<xsl:value-of select="php:function('lang', 'Organizer')"/>:
		</span>
		<xsl:if test="event/is_public=0">
			<br/>
			<xsl:value-of select="php:function('lang', 'Private event')"/>
		</xsl:if>
		<xsl:if test="event/is_public=1">
			<xsl:value-of select="event/organizer"/>
		</xsl:if>
	</div>
	<xsl:if test="event/is_public=1">
		<div class="tooltip-desc-btn">
			<span>
				<i class="fas fa-info-circle"></i>
			</span>
			<p class="tooltip-desc">
				<span class="d-block font-weight-normal">
					<!--<xsl:value-of select="event/contact_name"/>-->
					<xsl:if test="event/contact_email != ''">
						<br/>
						<xsl:value-of select="php:function('lang', 'contact_email')"/>: <xsl:value-of select="event/contact_email"/>
					</xsl:if>
					<xsl:if test="event/contact_phone != ''">
						<br/>
						<xsl:value-of select="php:function('lang', 'contact_phone')"/>: <xsl:value-of select="event/contact_phone"/>
					</xsl:if>
					<!--
										<xsl:if test="event/equipment != ''">
											<br/>
											<xsl:value-of select="event/equipment" disable-output-escaping="yes"/>
										</xsl:if>
					-->
				</span>
			</p>
		</div>
	</xsl:if>

	<xsl:if test="event/participant_limit > 0">
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
			</span>
			<xsl:value-of select="event/participant_limit"/>
		</div>
		<div class="actions">
			<a href="{event/show_link}" target="_blank" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'register participants')"/>
			</a>
		</div>
	</xsl:if>

	<xsl:if test="event/edit_link">
		<div class="actions">
			<a  href="{event/edit_link}" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'Edit event')"/>
			</a>
			<xsl:if test="user_can_delete_events = 1">
				<a href="{event/cancel_link}" class="btn btn-light mt-4">
					<xsl:value-of select="php:function('lang', 'Cancel event')"/>
				</a>
			</xsl:if>
		</div>
	</xsl:if>
	<xsl:if test="event/ical_link">
		<xsl:if test="event/copy_link">
			<a href="{event/copy_link}" target="_blank" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'Copy application')"/>
			</a>
		</xsl:if>
		<div class="actions">
			<a class="btn btn-light mt-4" href="{event/ical_link}" target="_blank">
				iCal
			</a>
		</div>
	</xsl:if>

</xsl:template>
