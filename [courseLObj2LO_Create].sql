USE [CMS_DB_02102020]
GO
/****** Object:  StoredProcedure [dbo].[courseLObj2LO_Create]    Script Date: 10/12/2020 3:52:31 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER PROCEDURE [dbo].[courseLObj2LO_Create]
  @COURSE_CODE VARCHAR(18),
  @courseLObjUUID uniqueidentifier,
  @user_id  VARCHAR(6)
AS 
BEGIN
    SET NOCOUNT ON; 

    INSERT INTO courseLObj2LO (stsrc, userIn, CRSE_CODE, courseLObjID, courseOutlineLearningOutcomeID, courseOutlineID) 
    SELECT  'I', @user_id, @COURSE_CODE, (SELECT courseLObjID FROM courseLObj WHERE id = @courseLObjUUID), lo.courseOutlineLearningOutcomeID, lo.courseOutlineID
    FROM    CMS_DB.dbo.courseOutlineLearningOutcome lo
    WHERE   lo.courseOutlineID IN (select CAST(cc.CRSE_ID as int) from ORACLE.dbo.PS_N_COURSE_CODE cc where cc.CRSE_CODE = @COURSE_CODE) 
            AND lo.stsrc NOT IN ('D')
    ORDER BY lo.priority ASC;
END
