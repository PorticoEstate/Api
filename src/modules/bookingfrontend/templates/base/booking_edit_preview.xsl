<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="booking-edit-preview-page-content" class="margin-top-content">
		<div class="container wrapper">
			<div class="location">
				<span>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/', '')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Home')" />
					</a>
				</span>
				<span>
					<xsl:value-of select="php:function('lang', 'Mass update')"/>
				</span>
			</div>
			<div class="row">
				<form action="" method="POST" class="col-md-8">
					<div class="col mb-4">
						<xsl:call-template name="msgbox"/>
					</div>
					<input type="hidden" name="repeat_until" value="{repeat_until}"/>
					<input type="hidden" name="recurring" value="{recurring}"/>
					<input type="hidden" name="outseason" value="{outseason}"/>
					<input type="hidden" name="season_id" value="{booking/season_id}"/>
					<input type="hidden" name="step" value="{step}"/>
					<input type="hidden" name="group_id" value="{group_id}"/>
					<input type="hidden" name="activity_id" value="{activity_id}"/>
					<input type="hidden" name="building_id" value="{booking/building_id}"/>
					<select size="10" class="form-control">
						<xsl:for-each select="bookings/results">
							<option>
								<xsl:attribute name="value">
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
							</option>
						</xsl:for-each>
					</select>
					<div class="col mt-5">
						<xsl:if test="count(bookings/results) &gt; 0">
							<div class="btn btn-light mr-4">
								<input type="submit" style="float: right;">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Update')"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:if>
					</div>
				</form>
				<div class="col-md-8">
					<xsl:choose>
						<xsl:when test="step = 2">
							<p>
								<xsl:value-of select="php:function('lang', '%1 bookings will be updated.', count(bookings/results))" />
							</p>
							<form action="" method="POST">
							</form>
						</xsl:when>
						<xsl:when test="step = 3">
							<p>
								<xsl:value-of select="php:function('lang', '%1 bookings were updated.', update_count)" />
								<div class="form-buttons">
									<a class="cancel">
										<xsl:attribute name="href">
											<xsl:value-of select="booking/cancel_link"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'Go back to calendar')" />
									</a>
								</div>
							</p>
						</xsl:when>
					</xsl:choose>
				</div>
			</div>
		</div>
	</div>
	<div class="push"></div>
</xsl:template>
